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
  public $secret;

  /**
   * @param string $user
   *   The username for your Trello account
   * @param string $apiKey
   *   The API key provided by Trello
   */
  public function __construct($username, $apiKey, $secret = '') {
    $this->username = $username;
    $this->apiKey = $apiKey;
    $this->secret = $secret;
  }

  /**
   * List boards that that a user has read access to
   *
   * @param string $user
   *   The name of the user to retrieve boards from.
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
   *   An object containing all boards that a user can read from that where
   *   that filter applies.
   */
  public function listBoards($user, $filter = 'all') {
    $url = $this->apiUrl('/members/' . $user . '/boards/' . $filter);
    $response = $this->buildRequest($url);
    return $response;
  }

  /**
   * Get an individual board fom Trello
   *
   * @param string $board
   *   The ID of the board to retrieve.
   *
   * @return
   *   An object containing information about the board
   */
  public function getBoard($board) {
    $url = $this->apiUrl('/boards/' . $board);
    $response = $this->buildRequest($url);
    return $response;
  }

  /**
   * Get a listing of cards from a Trello Board
   *
   * @param string $board
   *   The ID of the board to use.
   *
   * @return
   *   An object containing the cards that a user can read from the board.
   */
  public function listBoardCards($board) {
    $url = $this->apiUrl('/boards/' . $board . '/cards');
    $response = $this->buildRequest($url);
    return $response;
  }

  /**
   * Get a listing of the members of a Trello Board
   *
   * @param string $board
   *   The ID of the board to use.
   *
   * @return
   *   An object containing a list of the members of the board.
   */
  public function listBoardMembers($board) {
    $url = $this->apiUrl('/boards/' . $board . '/members');
    $response = $this->buildRequest($url);
    return $response;
  }
}
