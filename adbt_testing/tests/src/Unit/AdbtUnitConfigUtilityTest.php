<?php

namespace Drupal\Tests\adbt_testing\Unit;

use Drupal\adbt_testing\Service\AdbtTestingConfigUtility;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\adbt_testing\Service\AdbtTestingConfigUtility
 * @group adbt_testing
 */
class AdbtUnitConfigUtilityTest extends UnitTestCase {

  /**
   * The testing utility.
   *
   * @var \Drupal\adbt_testing\Service\AdbtTestingConfigUtility
   */
  protected $utility;

  /**
   * The mocked ban IP manager.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Drupal::$container is not initialized yet.
    // $this->configFactory = \Drupal::configFactory();
    // Let's mock the config factory.
    $this->configFactory = $this->createMock('Drupal\Core\Config\ConfigFactoryInterface');
    $this->utility = new AdbtTestingConfigUtility($this->configFactory);
  }

  /**
   * Tests a method getSiteName.
   *
   * @group mocking_unit
   * @covers Drupal\adbt_testing\Service\AdbtTestingConfigUtility::getSiteName
   */
  public function testGetSiteName() {
    $obj = new DummyData();
    $obj->set('name', 'Drush Site-Install');

    $this->configFactory->expects($this->once())
      ->method('get')
      ->willReturn($obj);

    $this->assertEquals('Drush Site-Install', $this->utility->getSiteName());
  }

}
