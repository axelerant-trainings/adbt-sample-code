<?php

namespace Drupal\fitness_challenge\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Controller routines to show heavy computed data.
 */
class ShowHeavyComputedDataController extends ControllerBase {

  const TIME_IN_SECONDS = 20;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Constructs a ShowHeavyComputedDataController object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
  */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cache.default')
    );
  }

  /**
   * Returns controller markup.
   *
   * @return array
   *   A render array representing the page content.
   */
  public function showdata() {
    // Show some heavy computed data.
    $cid = 'fitness_caching_heavy_computation';
    $info = $this->cache->get($cid);

    // Cache data if expired or not cached yet.
    if (!$info) {
      $data = [
        'time' => (int) time(),
        'complexdata' => 'TWI^FACE*API90807060504030201000',
      ];

      // Set the cache.
      $this->cache->set($cid, $data, time() + self::TIME_IN_SECONDS);

      $msg = $this->t('Cache expired or unavailable;
        A new computed data %data cached for @sec seconds!', [
          '%data' => $data['complexdata'],
          '@sec' => self::TIME_IN_SECONDS,
        ]);

      $this->messenger()->addWarning($msg);
    }
    else {
      $data = $info->data;
      $time_left = $data['time'] - ((int) time()) + self::TIME_IN_SECONDS;

      $msg = $this->t('Serving cached data %data for another @sec seconds.', [
        '%data' => $data['complexdata'],
        '@sec' => $time_left,
      ]);
      $this->messenger()->addStatus($msg);
    }

    return [
      '#markup' => 'Caching stores frequently accessed data in a temporary, high-speed storage layer, reducing retrieval time and minimizing repeated computations.',
    ];
  }

}