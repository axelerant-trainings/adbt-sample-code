<?php

namespace Drupal\adbt_caching\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides to demonstrate cache-context concept.
 *
 * @Block(
 *   id = "adbt_cache_context_concept",
 *   admin_label = @Translation("Cache Context Concept")
 * )
 */
class CacheContextConceptBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $prefix = $this->t(
      'Make sure the current page has <strong>?country=</strong> query parameter available.'
    );

    $announcement = $this->configFactory
      ->getEditable('custom.settings')
      ->get('announcement');

    // To cache data based on country query parameter.
    $build = [
      '#markup' => $prefix . '<br /><br />' . $announcement['value'],
      '#cache' => [
        'contexts' => [
          'url.query_args:country',
        ],
      ],
    ];

    return $build;
  }

}
