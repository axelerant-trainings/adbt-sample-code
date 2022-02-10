<?php

namespace Drupal\adbt_routing\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class to support dynamic routes.
 */
class DynamicRoute {

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
   * Registers dynamic routes for know node types.
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
        '/adbt/routing/dynamic-route/via-services/' . $id,

        // Route defaults:
        [
          '_controller' => '\Drupal\adbt_routing\Controller\SampleController::dynamic',
          '_title' => 'Dynamic route via services for ' . $label,
        ],

        // Route requirements:
        [
          '_permission'  => 'access content',
        ]
      );

      // Add the route under the name 'adbt_routing.dynamic_*'.
      $route_collection->add('adbt_routing.dynamic_route_via_service_' . $id, $route);
    }

    return $route_collection;
  }

}
