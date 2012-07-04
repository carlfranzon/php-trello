<?php

/**
 * @file
 * Defines abstract base class for all PHP-Trello tests.
 */

/**
 * Base class for PHP-Trello tests. All PHP-Trello tests should extend
 * this class.
 */
class TrelloBaseTest extends PHPUnit_Framework_TestCase {
  protected $client;
  protected $clientWrongKey;

  /**
   * Set up our basic tests
   */
  protected function setUp() {
    $this->client = new TrelloClient('brianaltenhofelusertest', '246bd36112b51ad571f1455152bf7dc7');
    $this->clientWrongKey = new TrelloClient('brianaltenhofelusertest', 'xxxxd36112b51ad571f1455152bfxxxx');
  }

  public function testApiUrl() {
    $expected = 'https://api.trello.com/1/members/' . $this->client->username . '?key=' . $this->client->apiKey;
    $result = $this->client->apiUrl('/members/' . $this->client->username, '');

    $this->assertTrue($expected == $result, 'Expected ' . $expected . ' | Got ' . $result);
  }

  public function testBuildRequest() {
    // Test that we can see ourselves on Trello
    $expected = 200;
    $result = $this->client->buildRequest($this->client->apiUrl('/members/' . $this->client->username, ''));
    $this->assertTrue($expected == $result->response->code, 'Expected HTTP Status Code 200');

    // Test that we can see other users on Trello
    $expected = 200;
    $result = $this->client->buildRequest($this->client->apiUrl('/members/brianaltenhofel', ''));
    $this->assertEquals($expected, $result->response->code, 'Successful request for data about another user');

    // Test that an invalid key will fail properly
    $expected = 401;
    $result = $this->clientWrongKey->buildRequest($this->client->apiUrl('/members/' . $this->client->username, ''));
    $this->assertEquals($expected, $result->response->code, 'Invalid key fails properly');

    // Test that an invalid username will fail properly
    $expected = 404;
    $result = $this->clientWrongKey->buildRequest($this->client->apiUrl('/members/admin', ''));
    $this->assertEquals($expected, $result->response->code, 'Invalid username fails properly');
  }
}
