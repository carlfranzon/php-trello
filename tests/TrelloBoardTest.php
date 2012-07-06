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
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for board listing');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for board listing returned an empty array');
  }

  /**
   * A good test suite makes sure that we do fail where we are supposed to
   * fail. This set of tests is to ensure that.
   */
  public function testBoardsControlledFailures() {
    // Test that listing boards with a presumably invalid username works
    $result = $this->client->listBoards('admin');
    $this->assertTrue('404' === $result->code, 'Request using invalid username failed to return 404 error');
  }

}
