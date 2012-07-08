<?php

/**
 * @file
 * Defines tests for Trello boards.
 */

/**
 * Class for testing Trello board-related functions
 */
class TrelloBoardTest extends TrelloBaseTest {

  protected $testBoard = '4ff49d494e4aee555cc4b712';
  protected $testBoardPublic = '4ff4f2eefe637f533a729e0e';

  public function setUp() {
    parent::setUp();
  }

  public function testListBoards() {
    // Test that we are using a proper endpoint and our key is valid
    $result = $this->client->listBoards($this->client->username);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for board listing.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for board listing returned nothing.');
  }

  public function testGetBoard() {
    // Test that we can get a board
    $result = $this->client->getBoard($this->testBoard);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for board.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for board listing returned nothing.');

    // Test that we can get a public board that is not self-owned and that we are not a member of
    $result = $this->client->getBoard($this->testBoardPublic);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for public unowned board.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for public unowned board returned nothing.');
  }

  public function testGetBoardCards() {
    // Test that we can get a listing of cards for our board.
    $result = $this->client->getBoardCards($this->testBoard);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for card listing for board.');
    // @TODO Test Board currently contains no cards
//    $data = $this->client->decode($result->data);
//    $this->assertTrue(!empty($data), 'Request for card listing for board returned nothing.');

    // Test that we can get a listing of cards of a public unowned board.
    $result = $this->client->getBoardCards($this->testBoardPublic);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for card listing for public unowned board.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for card listing for public unowned board returned nothing.');
  }

  /**
   * A good test suite makes sure that we do fail where we are supposed to
   * fail. This set of tests is to ensure that.
   */
  public function testBoardsControlledFailures() {
    // Test that listing boards with a presumably invalid username fails
    $result = $this->client->listBoards('admin');
    $this->assertTrue('404' === $result->code, 'Request using invalid username failed to return a 404 error.');
    // Test that retrieving a board with a presumably invalid board ID fails
    $result = $this->client->getBoard('000000000000000000000000');
    $this->assertTrue('404' === $result->code, 'Request for invalid board ID failed to return a 404 error.');
  }

}
