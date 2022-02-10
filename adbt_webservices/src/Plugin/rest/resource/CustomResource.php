<?php

namespace Drupal\adbt_webservices\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a Custom Resource.
 *
 * @RestResource(
 *   id = "custom_resource",
 *   label = @Translation("Custom Resource"),
 *   uri_paths = {
 *     "canonical" = "/adbt/webservices/custom_resource"
 *   }
 * )
 */
class CustomResource extends ResourceBase {

  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Response data.
   */
  public function get() {
    $response = [
      'message' => 'Hello message from custom resource.',
    ];

    return new ResourceResponse($response);
  }

}
