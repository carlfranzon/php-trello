<?php

/**
 * @file
 * Defines tests for Trello boards.
 */

/**
 * Class for testing Trello board-related functions
 */
class TrelloBoardTest extends TrelloBaseTest {

  public function setUp() {
    parent::setUp();
  }

  public function testListBoards() {
    // Test that we are using a proper endpoint and our key is valid
    $result = $this->client->listBoards($this->client->username);
    $this->assertTrue(!empty($result), 'Received an empty array');
  }

  /**
   * A good test suite makes sure that we do fail where we are supposed to
   * fail. This set of tests is to ensure that.
   */
  public function testBoardsControlledFailures() {
    // Test that listing boards with a presumably invalid username works
    $result= $this->client->listBoards('admin');
    $this->assertTrue();
  }

}
