<?php

namespace Drupal\adbt_caching\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides to demonstrate cache-tags concept.
 *
 * @Block(
 *   id = "adbt_cache_tags_concept",
 *   admin_label = @Translation("Cache Tags Concept")
 * )
 */
class CacheTagsConceptBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates CacheTagsConceptBlock object.
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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $articles = $this->entityTypeManager->getStorage('node')
      ->loadByProperties([
        'type' => 'article',
        'status' => 1,
      ]);

    if (empty($articles)) {
      return [
        '#markup' => $this->t('Create a few article nodes first.'),
      ];
    }

    $build = [
      '#theme' => 'article_cache_nodes',
      '#node_list' => $articles,
    ];

    // Also see getCacheTags(), getCacheMaxAge() methods.
    $cacheability = new CacheableMetadata();
    $cacheability->setCacheTags(['node_list:article', 'adbt_block']);
    $cacheability->applyTo($build);

    return $build;
  }

}
