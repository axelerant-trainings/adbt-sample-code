<?php

namespace Drupal\Tests\adbt_testing\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests custom config utility.
 *
 * @group adbt_testing
 */
class AdbtKernelUtilityConfigTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Mentioned modules are loaded such that their services and hooks are
    // available, but the install process has not been performed.
    'adbt_testing',
    'block',
    'path_alias',
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // \Drupal::service('adbt_testing.config_utility');
    // This^ also works but clean way to use.
    $this->utility = $this->container->get('adbt_testing.config_utility');
  }

  /**
   * The custom config utility.
   *
   * @var \Drupal\adbt_testing\Service\AdbtTestingConfigUtility
   */
  protected $utility;

  /**
   * Tests hook install and the utitlity method getSiteName.
   *
   * @group adbt_hook
   * @covers Drupal\adbt_testing\Service\AdbtTestingConfigUtility::getSiteName
   */
  public function testConfigUpdatedViaHookInstall() {
    // Load install file of $module.
    module_load_install('adbt_testing');
    // Running the expected function.
    adbt_testing_install();

    // $this->enableModules(['adbt_testing']);
    // $module_handler = $this->container->get('module_handler');
    // $module_filenames = $module_handler->invoke('adbt_testing', 'install');
    $this->assertEquals('ADBT Automated Testing', $this->utility->getSiteName());
  }

  /**
   * Tests block placement configuration.
   *
   * @group adbt_config
   */
  public function testBlockPlacementConfig() {
    $this->installConfig(['adbt_testing']);
    $config = \Drupal::config('block.block.showtrainingname')->get();

    // Debugging values via dump().
    dump($config);

    // Verifying block configuration.
    $this->assertSame('adbt_testing_training_name', $config['plugin']);
  }

}
