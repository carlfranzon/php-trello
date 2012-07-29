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
   * Get information about a member
   *
   * @param string $user
   *   The name or member ID of the user. Defaults to 'me' which retrieves
   *   information about the username associated with the supplied token.
   *
   * @return
   *   An object containing information about a given member
   */
  public function getMember($user = 'me') {
    $url = $this->apiUrl('/members/' . $user);
    $response = $this->buildRequest($url);
    return $response;
  }

  /**
   * List boards that that a user has read access to
   *
   * @param string $user
   *   The name of the user to retrieve boards from.
   * @param array $arguments (optional)
   *   An array containing arguments to be sent to Trello to alter board listings.
   *   Multiple values for any key should be comma separated.
   *
   *   Valid key-value pairs are:
   *   - filter
   *     - none
   *     - members
   *     - organization
   *     - public
   *     - open
   *     - closed
   *     - pinned
   *     - unpinned
   *     - all (default)
   *   - fields
   *     - name
   *     - desc
   *     - closed
   *     - idOrganization
   *     - invited
   *     - pinned
   *     - url
   *     - prefs
   *     - invitations
   *     - memberships
   *     - labelNames
   *     - all (default)
   *   - actions
   *     - addAttachmentToCard
   *     - addChecklistToCard
   *     - addMemberToBoard
   *     - addMemberToCard
   *     - addMemberToOrganization
   *     - commentCard
   *     - copyCommentCard
   *     - convertToCardFromCheckItem
   *     - copyBoard
   *     - createBoard
   *     - createCard
   *     - copyCard
   *     - createList
   *     - createOrganization
   *     - deleteAttachmentFromCard
   *     - deleteBoardInvitation
   *     - moveCardFromBoard
   *     - moveCardToBoard
   *     - removeAdminFromBoard
   *     - removeAdminFromOrganization
   *     - removeChecklistFromCard
   *     - removeFromOrganizationBoard
   *     - removeMemberFromCard
   *     - updateBoard
   *     - updateCard
   *     - updateCheckItemStateOnCard
   *     - updateMember
   *     - updateOrganization
   *     - updateCard:idList
   *     - updateCard:closed
   *     - updateCard:desc
   *     - updateCard:name
   *     - all (default)
   *   - actions_limit
   *     Any number between 1 and 1000 (default 50).
   *   - actions_format
   *     - count
   *     - list (default)
   *   - actions_since
   *     A date, NULL, or lastView
   *   - action_fields
   *     - idMemberCreator
   *     - data
   *     - type
   *     - date
   *     - all (default)
   *
   * @return
   *   An object containing all boards that a user can read from that where
   *   that filter applies.
   */
  public function listBoards($user, $arguments = array()) {
    $url = $this->apiUrl('/members/' . $user . '/boards');
    $response = $this->buildRequest($url, $arguments);
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
   * Get a card from a board by the card ID
   *
   * @param string $board
   *   The ID of the board to use.
   * @param string $card
   *   The ID of the card to retrieve.
   * @param array $arguments
   *   An array of arguments to send to Trello to modify Card output.
   *
   * @return
   *   An object containing information about the card from the board
   */
  public function getBoardCard($board, $card, $arguments = array()) {
    $url = $this->apiUrl('/boards/' . $board . '/cards/' . $card);
    $response = $this->buildRequest($url, $arguments);
    return $response;
  }

  /**
   * Get a card by the card ID
   *
   * @param string $card
   *   The ID of the card to retrieve.
   * @param array $arguments
   *   An array of arguments to send to Trello to modify Card output.
   *
   * @return
   *   An object containing information about the requested card.
   */
  public function getCard($card, $arguments = array()) {
    $url = $this->apiUrl('/cards/' . $card);
    $response = $this->buildRequest($url, $arguments);
    return $response;
  }

  /**
   * Get list of members for a card.
   *
   * @param string $card
   *   The ID of the card to retrieve.
   * @param array $arguments
   *   An array of arguments to send to Trello to modify Card output.
   *
   * @return
   *   An object containing information about the requested card.
   */
  public function listCardMembers($card, $arguments = array()) {
    $url = $this->apiUrl('/cards/' . $card . '/members');
    $response = $this->buildRequest($url, $arguments);
    return $response;
  }

  /**
   * Get information about the board that a Card belongs to.
   *
   * @param string $card
   *   The ID of the card to retrieve.
   * @param array $arguments
   *   An array of arguments to send to Trello to modify Card output.
   *
   * @return
   *   An object containing information about the board for the request card.
   */
  public function getCardBoard($card, $arguments = array()) {
    $url = $this->apiUrl('/cards/' . $card . '/board');
    $response = $this->buildRequest($url, $arguments);
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

  /**
   * Get a listing of boards for a member
   *
   * @param string $member
   *   The ID or username of the member
   *
   * @return
   *   An object containing a list of the boards for a member.
   */
  public function listMemberBoards($member, $arguments) {
    $url = $this->apiUrl('/members/' . $member . '/boards');
    $response = $this->buildRequest($url, $arguments);
    return $response;
  }
}
