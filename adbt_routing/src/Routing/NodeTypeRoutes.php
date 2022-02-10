<?php

namespace Drupal\adbt_routing\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines dynamic routes.
 */
class NodeTypeRoutes implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new NodeTypeRoutes object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $route_collection = new RouteCollection();

    $node_types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();

    foreach ($node_types as $node_type) {
      $id = $node_type->id();
      $label = $node_type->label();

      $route = new Route(
        // Path to attach this route to:
        '/adbt/routing/dynamic-route/' . $id,

        // Route defaults:
        [
          '_controller' => '\Drupal\adbt_routing\Controller\SampleController::dynamic',
          '_title' => 'Dynamic route for ' . $label,
        ],

        // Route requirements:
        [
          '_permission'  => 'access content',
        ]
      );

      // Add the route under the name 'adbt_routing.dynamic_*'.
      $route_collection->add('adbt_routing.dynamic_route_' . $id, $route);
    }

    return $route_collection;
  }

}
