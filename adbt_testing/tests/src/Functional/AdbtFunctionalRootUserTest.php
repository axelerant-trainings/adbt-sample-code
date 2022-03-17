<?php

namespace Drupal\Tests\adbt_testing\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test cases related to admin user.
 *
 * @group adbt_testing
 */
class AdbtFunctionalRootUserTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Verify admin links available for root user.
   *
   * @group admin_user
   */
  public function testAdminLinksAvailableForRoot() {

    // Start the session.
    $session = $this->assertSession();

    // Login as admin account.
    $this->drupalLogin($this->rootUser);

    // Make sure the URL appears when re-editing the action.
    $this->clickLink('Content');

    $msg = "No content available.";
    $session->pageTextContains($msg);

    $this->clickLink('Add content');
    $msg = 'Use articles for time-sensitive content like news, press releases or blog posts.';
    $session->pageTextContains($msg);
  }

}
