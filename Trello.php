<?php

/**
 * @file
 * Contains base class for PHP-Trello
 */

/**
 * Class containing essential functions for interacting with the Trello API
 */
class Trello {

  protected $username;
  protected $apiKey;

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
  protected function apiUrl($path, $query) {
    $url = 'https://api.trello.com/1' . $path . '?key=' . $this->apiKey;

    if (isset($query)) {
      $url .= '&' . urlencode($query);
    }

    return $url;
  }

}
