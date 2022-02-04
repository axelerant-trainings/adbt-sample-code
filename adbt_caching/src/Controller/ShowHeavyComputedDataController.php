<?php

namespace Drupal\adbt_caching\Controller;

use Drupal\adbt_caching\Service\AdbtCachingUtility;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines to show heavy computed date.
 */
class ShowHeavyComputedDataController extends ControllerBase {

  /**
   * The utility.
   *
   * @var \Drupal\adbt_caching\Service\AdbtCachingUtility
   */
  protected $utility;

  /**
   * Constructs a ShowHeavyComputedDataController object.
   *
   * @param \Drupal\adbt_caching\Service\AdbtCachingUtility $adbt_caching_utility
   *   The adbt utility.
   */
  public function __construct(AdbtCachingUtility $adbt_caching_utility) {
    $this->utility = $adbt_caching_utility;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('adbt_caching.utility')
    );
  }

  /**
   * Returns controller markup.
   *
   * @return array
   *   A render array representing the page content.
   */
  public function showdata() {
    $this->utility->getSomeHeavyComputationThing();

    return [
      '#markup' => 'hello world!',
    ];
  }

}
