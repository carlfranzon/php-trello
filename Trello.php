<?php

/**
 * @file
 * Contains base class for PHP-Trello
 */

/**
 * Class containing essential functions for interacting with the Trello API
 */
class Trello {

  public $username;
  public $apiKey;

  /**
   * @param string $user
   *   The username for your Trello account
   * @param string $apiKey
   *   The API key provided by Trello
   */
  public function __construct($username, $apiKey) {
    $this->username = $username;
    $this->apiKey = $apiKey;
  }

  /**
   * Build the URL for accessing Trello
   *
   * @param $path
   *
   * @return string
   */
  public function apiUrl($path, $query) {
    $url = 'https://api.trello.com/1' . $path . '?key=' . $this->apiKey;

    if (isset($query)) {
      $url .= '&' . urlencode($query);
    }

    return $url;
  }

  /**
   * Build the HTTP request for accessing Trello
   * We use stream_socket_client() to avoid external dependencies
   *
   * @param string $url
   *   The fully qualified URL we want to connect to
   * @param array $options (optional)
   *   An array containing the following elements:
   *   - headers: An array containing headers to send with the request.
   *   - method: The method to use for this request.
   *   - data: The body of the request.
   *   - timeout: The maximum number of seconds this request can take.
   *   - context: A context resource created with stream_context_create().
   *
   * @return object
   *   An object containing the following information:
   *   - request: A string containing the request bod that was sent.
   *   - code: HTTP Status Code.
   *   - protocol: HTTP protocol.
   *   - status_message: HTTP Status message, if received.
   *   - error: Error message if an error was encountered.
   *   - headers: An array containing HTTP response headers.
   *   - data: The body of the response.
   */
  public function buildRequest($url, $options) {
    $result = new stdClass();

    $uri = @parse_url($url);

    if (FALSE == $uri) {
      $result->error = 'unable to parse URL';
      $result->code = -1001;
      return $result;
    }

    if (!isset($url['scheme'])) {
      $result->error = 'missing schema';
      $result->code = -1002;
      return $result;
    }

    // Merge default options
    $options += array(
      'headers' => array(),
      'method' => 'GET',
      'data' => NULL,
      'timeout' => 30.0,
      'context' => NULL,
    );

    // Ensure that timeout is a float
    $options['timeout'] = (float) $options['timeout'];

    switch ($uri['scheme']) {
      case 'http':
      case 'feed':
        $port = isset($uri['port']) ? $uri['port'] : 80;
        $socket = 'tcp://' . $uri['host'] . ':' . $port;
        // RFC 2616: "non-standard ports MUST, default ports MAY be included".
        // We don't add the standard port to prevent from breaking rewrite rules
        // checking the host that do not take into account the port number.
        $options['headers']['Host'] = $uri['host'] . ($port != 80 ? ':' . $port : '');
        break;
      case 'https':
        // Note: Only works when PHP is compiled with OpenSSL support.
        $port = isset($uri['port']) ? $uri['port'] : 443;
        $socket = 'ssl://' . $uri['host'] . ':' . $port;
        $options['headers']['Host'] = $uri['host'] . ($port != 443 ? ':' . $port : '');
        break;
      default:
        $result->error = 'invalid schema ' . $uri['scheme'];
        $result->code = -1003;
        return $result;
    }

    if (empty($options['context'])) {
      $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout']);
    }
    else {
      // Create a stream with context. Allows verification of a SSL certificate.
      $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout'], STREAM_CLIENT_CONNECT, $options['context']);
    }

    // Make sure the socket opened properly.
    if (!$fp) {
      $result->code = -$errno;
      $result->error = trim($errstr) ? trim($errstr) : t('Error opening socket @socket', array('@socket' => $socket));
      return $result;
    }

    // Build our path
    $path = isset($uri['path']) ? $uri['path'] : '/';
    if (isset($uri['query'])) {
      $path .= '?' . $uri['query'];
    }

    // Merge the default headers.
    $options['headers'] += array(
      'User-Agent' => 'PHP-Trello',
    );

    // Content-Length is only required for PUT or POST requests
    $content_length = strlen($options['data']);
    if ($content_length > 0 || $options['method'] == 'POST' || $options['method'] == 'PUT') {
      $options['headers']['Content-Length'] = $content_length;
    }

    $request = $options['method'] . ' ' . $path . " HTTP/1.0\r\n";
    foreach ($options['headers'] as $name => $value) {
      $request .= $name . ': ' . trim($value) . "\r\n";
    }
    $request .= "\r\n" . $options['data'];
    $result->request = $request;

    // Fetch response
    $info = stream_get_meta_data($fp);
    $alive = !$info['eof'] && !$info['timed_out'];
    $response = '';

    while ($alive) {
      // Calculate how much time is left of the original timeout value.
      $timeout = $options['timeout'] - timer_read(__FUNCTION__) / 1000;
      if ($timeout <= 0) {
        $info['timed_out'] = TRUE;
        break;
      }
      stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
      $chunk = fread($fp, 1024);
      $response .= $chunk;
      $info = stream_get_meta_data($fp);
      $alive = !$info['eof'] && !$info['timed_out'] && $chunk;
    }
    fclose($fp);

    if ($info['timed_out']) {
      $result->code = -1;
      $result->error = 'request timed out';
      return $result;
    }

    // Parse response headers
    list($response, $result->data) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
    $response = preg_split("/\r\n|\n|\r/", $response);

    // Parse the response status line.
    list($protocol, $code, $status_message) = explode(' ', trim(array_shift($response)), 3);
    $result->protocol = $protocol;
    $result->status_message = $status_message;

    $result->headers = array();

    // Parse the response headers.
    while ($line = trim(array_shift($response))) {
      list($name, $value) = explode(':', $line, 2);
      $name = strtolower($name);
      $result->headers[$name] = trim($value);
    }

    $responses = array(
      100 => 'Continue',
      101 => 'Switching Protocols',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Time-out',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Large',
      415 => 'Unsupported Media Type',
      416 => 'Requested range not satisfiable',
      417 => 'Expectation Failed',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Time-out',
      505 => 'HTTP Version not supported',
    );

    if (!isset($responses[$code])) {
      $code = floor($code / 100) * 100;
    }
    $result->code = $code;

    switch ($code) {
      case 200: // OK
      case 304: // Not modified
        break;
      case 301: // Moved permanently
      case 302: // Moved temporarily
      case 307: // Moved temporarily
        $location = $result->headers['location'];
        $options['timeout'] -= timer_read(__FUNCTION__) / 1000;
        if ($options['timeout'] <= 0) {
          $result->code = HTTP_REQUEST_TIMEOUT;
          $result->error = 'request timed out';
        }
        break;
      default:
        $result->error = $status_message;
    }

    return $result;
  }

}
