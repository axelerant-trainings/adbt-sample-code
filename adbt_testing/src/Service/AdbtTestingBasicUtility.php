<?php

namespace Drupal\adbt_testing\Service;

/**
 * Defines ADBT testing basic utility service.
 */
class AdbtTestingBasicUtility {

  /**
   * Computes the sum value of given numbers.
   */
  public function addNumbers(array $numbers) {
    $sum = 0;
    foreach ($numbers as $number) {
      $sum += $number;
    }

    return $sum;
  }

}
