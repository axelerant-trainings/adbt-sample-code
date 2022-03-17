<?php

namespace Drupal\Tests\adbt_testing\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Installs the config translation module on a site installed in non english.
 *
 * @group config_translation
 */
class AdbtFunctionalHookEntityPreSaveTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'adbt_testing',
  ];

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Tests a method addUs.
   *
   * @group node_presave
   * @dataProvider shareNodeTypes
   */
  public function testNodeTitlePreSave($type) {
    // Create the user with the appropriate permission.
    $user = $this->drupalCreateUser([
      'create ' . $type . ' content',
    ]);

    // Start the session.
    $session = $this->assertSession();

    // Login as our account.
    $this->drupalLogin($user);

    // Visit node add page.
    $this->drupalGet('node/add/' . $type);

    // Assert status code.
    $session->statusCodeEquals(200);

    $node_title = 'My Node';
    $edit = [
      'title[0][value]' => $node_title,
    ];
    $this->submitForm($edit, 'Save');

    $msg = "$type $node_title #Awesome! has been created.";
    $this->assertSession()->pageTextContains($msg);

    // Assert we are on node/1 page.
    $session->addressEquals('node/1');
  }

  /**
   * Tests a method addUs.
   *
   * @group node_presave
   * @dataProvider shareNodeTypes
   */
  public function testUnpublishedNodeTitlePreSave($type) {
    // Start the session.
    $session = $this->assertSession();

    // Login as admin account.
    $this->drupalLogin($this->rootUser);

    // Visit node add page.
    $this->drupalGet('node/add/' . $type);

    $page = $this->getSession()->getPage();

    // Add a node with text rendered via the Plain Text format.
    $node_title = 'My test content';
    $page->fillField('Title', $node_title);
    $page->fillField('Body', '<p><a style="color:#ff0000;" foo="bar" hreflang="en" href="https://example.com"><abbr title="National Aeronautics and Space Administration">NASA</abbr> is an acronym.</a></p>');

    $page->uncheckField('Published');
    $page->pressButton('Save');

    // Assert access denied.
    $session->statusCodeEquals(200);

    $msg = "$type $node_title #Awesome! has been created.";
    $this->assertSession()->pageTextContains($msg);

    // Assert we are on node/1 page.
    $session->addressEquals('node/1');
  }

  /**
   * Data provider for testNodeTitlePreSave.
   *
   * Return an array of arrays, each of which contains the parameter values to
   * be used in one invocation of the testAddUsWithDataproviders test function.
   */
  public function shareNodeTypes() {
    return [
      ['article'],
      ['page'],
    ];
  }

}
