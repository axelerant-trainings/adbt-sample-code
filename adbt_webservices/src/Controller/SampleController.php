<?php

namespace Drupal\adbt_webservices\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Sample controller.
 */
class SampleController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
    // @todo Update the code to a working sample.
    $host = \Drupal::request()->getSchemeAndHttpHost();

    // JSON API.
    $response = \Drupal::httpClient()->get($host . '/jsonapi/node/page', [
      'auth' => ['admin', 'admin'],
    ]);

    $jsondata = json_decode($response->getBody()->getContents());

    $markup = '';
    $count = 0;
    foreach ($jsondata->data as $item) {
      $count++;
      $markup .= $count . '. ' . $item->attributes->title . PHP_EOL;
    }

    $build = [
      '#markup' => $markup,
      '#attached' => [
        'library' => ['adbt_webservices/adbt_webservices'],
      ],
    ];

    return $build;
  }

}
