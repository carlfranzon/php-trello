<?php

/**
 * @file
 * Contains base class for PHP-Trello
 */

/**
 * Class for handling client-related data
 */
class TrelloClient extends Trello {

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
   * List boards that that a user has read access to
   *
   * @param string $filter (optional)
   *   Valid options are:
   *   - none
   *   - members
   *   - organization
   *   - public
   *   - open
   *   - closed
   *   - pinned
   *   - unpinned
   *   - all
   *
   * @return
   *   An array of TrelloBoard objects containing board ID's
   */
  public function listBoards($filter = 'all') {
    $url = $this->apiUrl('/members/' . $this->username . '/boards/' . $filter);
    $response = $this->buildRequest($url);
    $data = json_decode($response->data);

    $results = array();
    foreach($data as $info) {
      $board = new TrelloBoard($info->id);
      $results[] = $board;
    }

    return $results;
  }
}
