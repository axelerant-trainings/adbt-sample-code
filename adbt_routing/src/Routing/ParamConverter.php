<?php

namespace Drupal\adbt_routing\Routing;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Converts parameters and upcasts.
 */
class ParamConverter implements ParamConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    if (empty($value) || $definition['type'] !== 'mycustom-upcast') {
      return NULL;
    }

    // Upcasting parameter with name.
    if ($name == 'user') {
      return $value * $value;
    }
    elseif ($name == 'message') {
      return $value . ' - A message from Axelerant!';
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] == 'mycustom-upcast');
  }

}
