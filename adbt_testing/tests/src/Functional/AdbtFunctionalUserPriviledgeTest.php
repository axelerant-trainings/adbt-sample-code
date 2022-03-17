<?php

namespace Drupal\Tests\adbt_testing\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test cases related to content authors.
 *
 * @group adbt_testing
 */
class AdbtFunctionalUserPriviledgeTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Test priviledge user with improper permission cannot create node.
   *
   * @group priviledged_user
   */
  public function testUnpriviledgedUserNodeAdd() {
    // Create the user with the inappropriate permission.
    $user = $this->drupalCreateUser([
      'edit any article content',
    ]);

    // Start the session.
    $session = $this->assertSession();

    // Login as our account.
    $this->drupalLogin($user);

    // Visit node add page.
    $this->drupalGet('node/add/article');

    // Assert access denied.
    $session->statusCodeEquals(403);
  }

  /**
   * Test priviledge user with proper permission can create node.
   *
   * @group priviledged_user
   */
  public function testPriviledgedUserNodeAdd() {
    // Create the user with the appropriate permission.
    $user = $this->drupalCreateUser([
      'create article content',
    ]);

    // Start the session.
    $session = $this->assertSession();

    // Login as our account.
    $this->drupalLogin($user);

    // Visit node add page.
    $this->drupalGet('node/add/article');

    // Assert success.
    $session->statusCodeEquals(200);
  }

}
