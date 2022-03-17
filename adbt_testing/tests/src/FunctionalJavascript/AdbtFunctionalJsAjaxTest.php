<?php

namespace Drupal\Tests\adbt_testing\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Testing custom AJAX form.
 *
 * @coversDefaultClass \Drupal\adbt_testing\Form\AjaxShowMySelectionForm
 * @group adbt_testing
 */
class AdbtFunctionalJsAjaxTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'adbt_testing',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'bartik';

  /**
   * Tests action plugins with AJAX save their configuration.
   */
  public function testAjaxFormThrobberAndResult() {
    // Login with root user.
    $this->drupalLogin($this->rootUser);

    $session = $this->getSession();
    $web_assert = $this->assertSession();
    $page = $session->getPage();

    // Visit our example AJAX form page.
    $url = Url::fromRoute('adbt_testing.ajax_form');
    $this->drupalGet($url);

    // No throbber element initially.
    $this->assertNull($web_assert->waitForElement('css', '.ajax-progress-throbber', 5000), 'There is no throbber element.');

    // Interact with the form and verify throbber element.
    $page->selectFieldOption('Select Number', '3');
    $this->assertNotNull($web_assert->waitForElement('css', '.ajax-progress-throbber'), 'Throbber element appeared.');
    $web_assert->pageTextContains('Please wait...');
    $web_assert->assertNoElementAfterWait('css', '.ajax-progress-throbber');

    // Confirm AJAX process result.
    $web_assert->elementContains('css', '#edit-output', '<h2>Your favourite number is <i>Three</i></h2>');
  }

  /**
   * Tests creating screenshots for home page.
   */
  public function testCreateScreenshotHomePage() {
    $screenshot_path = \Drupal::root() . '/sites/default/files/simpletest/test-ajax-form-snapshot.png';

    $this->createScreenshot($screenshot_path);
    $this->assertFileExists($screenshot_path);
  }

}
