<?php

namespace Drupal\adbt_testing\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Defines ADBT testing config utility service.
 */
class AdbtTestingConfigUtility {

  /**
   * The config factory.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Returns site name.
   */
  public function getSiteName() {
    $config = $this->configFactory->get('system.site');
    return $config->get('name');
  }

}
