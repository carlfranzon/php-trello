<?php

/**
 * @file
 * Defines tests for Trello members.
 */

/**
 * Class for testing Trello member-related functions
 */
class TrelloMemberTest extends TrelloBaseTest {

  public function setUp() {
    parent::setUp();
  }

  public function testGetMember() {
    // Test that we can get information about ourself
    // @TODO This requires tokens to be available
//    $result = $this->client->getMember();
//    $this->assertTrue('200' === $result->code, 'Unsuccessful request for member.');
//    $data = $this->client->decode($result->data);
//    $this->assertTrue(!empty($data), 'Request for member returned nothing.');

    // Test that we can get information about ourself using our username
    $result = $this->client->getMember($this->client->username);
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for member.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for member returned nothing.');

    // Test that we can get information about another user
    $result = $this->client->getMember('brianaltenhofel');
    $this->assertTrue('200' === $result->code, 'Unsuccessful request for member.');
    $data = $this->client->decode($result->data);
    $this->assertTrue(!empty($data), 'Request for member returned nothing.');
  }

}
