<?php

namespace Drupal\adbt_caching\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides to demonstrate max-age concept.
 *
 * @Block(
 *   id = "adbt_max_age_concept",
 *   admin_label = @Translation("Max Age Concept")
 * )
 */
class MaxAgeConceptBlock extends BlockBase implements ContainerFactoryPluginInterface {

  const MAX_AGE_TIME = 20;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Tests the test access block.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Shows time including seconds.
    $full_time = $this->dateFormatter->format(time(), 'html_time');

    $build = [
      '#markup' => $this->t('Unfortunately Max-Age does not work for Anonymous users. <br /><br />But for authenticated user this super time value <strong>%time</strong> will be cached for another <strong>%age</strong> seconds.', [
        '%age' => self::MAX_AGE_TIME,
        '%time' => $full_time,
      ]),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Mention exact number of seconds.
    return self::MAX_AGE_TIME;
  }

}
