<?php

namespace Drupal\Tests\adbt_testing\Unit;

use Drupal\adbt_testing\Service\AdbtTestingBasicUtility;
use Drupal\Tests\UnitTestCase;

/**
 * Testing module's basic utility service.
 *
 * @coversDefaultClass \Drupal\adbt_testing\Service\AdbtTestingBasicUtility
 * @group adbt_testing
 */
class AdbtUnitBasicUtilityTest extends UnitTestCase {

  /**
   * The basic utility.
   *
   * @var \Drupal\adbt_testing\Service\AdbtTestingBasicUtility
   */
  protected $utility;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->utility = new AdbtTestingBasicUtility();
  }

  /**
   * Tests a method addNumbers.
   *
   * @group add_numbers
   * @covers Drupal\adbt_testing\Service\AdbtTestingBasicUtility::addNumbers
   */
  public function testAddNumbers() {
    // On fail $message will be shown.
    $message = 'Incorrect result for service method addNumbers()';
    $this->assertEquals(7, $this->utility->addNumbers([3, 4]), $message);
  }

  /**
   * Tests method addNumbers with multiple data.
   *
   * @group add_numbers
   * @covers Drupal\adbt_testing\Service\AdbtTestingBasicUtility::addNumbers
   * @dataProvider addNumbersDataValues
   */
  public function testAddNumbersWithDataproviders($inputs, $expected_result) {
    $this->assertEquals($expected_result, $this->utility->addNumbers($inputs));
  }

  /**
   * Data provider for testMethodWithDataproviders.
   *
   * Return an array of arrays, each of which contains the parameter values to
   * be used in one invocation of testMethodWithDataproviders test function.
   */
  public function addNumbersDataValues() {
    return [
      [
        [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        55,
      ],
      [
        [25],
        25,
      ],
      [
        [100, 200, 500],
        800,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() : void {
    dump("Clean up completed! - " . __CLASS__);
  }

}
