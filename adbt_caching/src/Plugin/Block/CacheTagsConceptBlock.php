<?php

namespace Drupal\adbt_caching\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

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
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('renderer'),
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

      '#cache' => [
        'tags' => [
          'node_list:article',
          'adbt_block',
        ],
      ],
    ];

    // Using generic class to inject cache metadata.
    // Un-comment below two lines to use CacheableMetadata.
    // You may need to unset $build['#cache']['tags'].
    /*
    // $cacheability = new \Drupal\Core\Cache\CacheableMetadata();
    // $cacheability->setCacheTags(['node_list:article', 'adbt_block']);
    // $cacheability->applyTo($build);
     */

    // Adding dependent cache metadata.
    // Merges the cache contexts, cache tags and max-age of the config object
    // and user entity that the render array depend on.
    // Un-comment below lines, to inject cache metadata of articles.
    /*
    // foreach ($articles as $article) {
    //   $this->renderer->addCacheableDependency($build, $article);
    // }
     */

    return $build;
  }

}
