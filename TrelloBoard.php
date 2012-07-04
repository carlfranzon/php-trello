<?php

/**
 * @file
 * Contains class for Trello Boards
 */

/**
 * Class containing essential functions for interacting with Trello Boards via
 * the Trello API
 */
class TrelloBoard extends Trello {

  public $id;

  public function __construct($id) {
    $this->boardID = $id;
  }

  public function listBoards() {
    $url = $this->apiUrl('/members/' . $this->username . '/boards/all');
    $response = $this->buildRequest($url);
    $data = json_decode($response->data);

    $results = array();
    foreach($data as $board) {
      $board = new TrelloBoard($board->id);
      $results[] = $board;
    }

    return $results;
  }

}
