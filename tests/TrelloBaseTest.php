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

  /**
   * Set up our basic tests
   */
  protected function setUp() {
    $this->client = new Trello('brianaltenhofelusertest', '246bd36112b51ad571f1455152bf7dc7');
  }

  public function testApiUrl() {
    $expected = 'https://api.trello.com/1/members/' . $this->client->username . '?key=' . $this->client->apiKey;
    $result = $this->apiUrl('/members/' . $this->client->username, '');

    $this->assertEquals($expected, $result, 'Successfully build apiUrl ' . $expected);
  }

}
