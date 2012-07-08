<?php

/**
 * @file
 * Defines tests for Trello boards.
 */

/**
 * Class for testing Trello board-related functions
 */
class TrelloBoardTest extends TrelloBaseTest {

  protected $board = '4ff49d494e4aee555cc4b712';

  public function setUp() {
    parent::setUp();
  }

  public function testListBoards() {
    // Test that we are using a proper endpoint and our key is valid
    $result = $this->client->listBoards($this->client->username);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for board listing.');
    $data = $this->client->decode($this->client->listBoards($this->client->username)->data);
    $this->assertTrue(!empty($data), 'Request for board listing returned nothing.');
  }

  public function testGetBoard() {
    // Test that we can get a board
    $result = $this->client->getBoard($board);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for board.');
    $data = $this->client->decode($this->client->getBoard($board)->data);
    $this->assertTrue(!empty($data), 'Request for board listing returned nothing.');
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
