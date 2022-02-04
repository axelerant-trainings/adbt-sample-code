<?php

namespace Drupal\adbt_caching\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines ADBT Caching Utility service.
 */
class AdbtCachingUtility {

  use StringTranslationTrait;

  const TIME_IN_SECONDS = 20;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Constructs a new AdbtCachingUtility object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(MessengerInterface $messenger, CacheBackendInterface $cache) {
    $this->messenger = $messenger;
    $this->cache = $cache;
  }

  /**
   * Computes heavy data and cache it.
   */
  public function getSomeHeavyComputationThing() {
    $cid = 'adbt_caching_heavy_computation';
    $info = $this->cache->get($cid);

    // Cache data if expired or not cached yet.
    if (!$info) {
      $data = [
        'time' => (int) time(),
        'complexdata' => 'TWI^FACE*API90807060504030201000',
      ];

      // Set the cache.
      // Cache tags wont's show up in X-Drupal-Cache-Tags,
      // but will get effected by adbt_info.
      $this->cache->set($cid, $data, time() + self::TIME_IN_SECONDS, [
        'adbt_info',
        'adbt_new',
      ]);

      $msg = $this->t('Cache expired or unavailable;
        A new computed data %data cached for @sec seconds!', [
          '%data' => $data['complexdata'],
          '@sec' => self::TIME_IN_SECONDS,
        ]);

      $this->messenger->addWarning($msg);
    }
    else {
      $data = $info->data;
      $time_left = $data['time'] - ((int) time()) + self::TIME_IN_SECONDS;

      $msg = $this->t('Serving cached data %data for another @sec seconds).', [
        '%data' => $data['complexdata'],
        '@sec' => $time_left,
      ]);
      $this->messenger->addStatus($msg);
    }
  }

}
