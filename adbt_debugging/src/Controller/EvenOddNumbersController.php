<?php

namespace Drupal\adbt_debugging\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for identifying numbers.
 */
class EvenOddNumbersController extends ControllerBase {

  /**
   * Returns page content.
   *
   * @param int $number
   *   A number to indentify the type.
   *
   * @return array
   *   A render array representing the page content.
   */
  public function content($number = NULL) {

    if ($number % 2 == 0) {
      $type = 'Even';
    }
    else {
      $type = 'Odd';
    }

    for ($count = -1; $count < 99; $count++) {
      $title = $this->getTitle();
    }

    return [
      '#theme' => 'creative_template',
      '#type' => $type,
      '#attached' => [
        'library' => [
          'adbt_debugging/script',
        ],
      ],
    ];
  }

  /**
   * Returns some random title value.
   *
   * @return string
   *   Random title string.
   */
  private function getTitle() {
    return 'Hello ' . time();
  }

}
