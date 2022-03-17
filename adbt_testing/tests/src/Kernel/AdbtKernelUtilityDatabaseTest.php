<?php

namespace Drupal\Tests\adbt_testing\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Tests custom and entity schema installed in database.
 *
 * @group adbt_testing
 */
class AdbtKernelUtilityDatabaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'adbt_testing',
    'system',
    'user',
    'field',
    'filter',
    'text',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // \Drupal::service('adbt_testing.config_utility');
    // This^ also works but clean way to use.
    $this->utility = $this->container->get('adbt_testing.database_utility');
  }

  /**
   * The testing utility.
   *
   * @var \Drupal\adbt_testing\Service\AdbtTestingDatabaseUtility
   */
  protected $utility;

  /**
   * Tests custom database interactions.
   *
   * @group adbt_schema
   */
  public function testCustomDatabaseInteractions() {

    // Instal custom table adbt_testing_testimonial.
    $this->installSchema('adbt_testing', 'adbt_testing_testimonial');

    $user = 111;
    // Assert the first entry done with ID 1.
    $this->assertEquals('1', $this->utility->addTestimonial($user, 'msg - ' . $user));

    $user = 222;
    // Assert the second entry done with ID 2.
    $this->assertEquals('2', $this->utility->addTestimonial($user, 'msg - ' . $user));

    $testimonials = $this->utility->getUserTestimonial();
    $this->assertIsArray($testimonials);
    $this->assertCount('2', $testimonials);
  }

  /**
   * Tests entity schema installation.
   *
   * @group adbt_schema
   */
  public function testEntitySchemaWithOperations() {

    // Supporting schema for entity node.
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
    $this->installConfig(['node', 'field']);

    // Create a custom node type.
    $node_type = NodeType::create([
      'type' => $this->randomMachineName(),
      'name' => $this->randomString(),
    ]);
    $node_type->save();

    // Create a node.
    $title = $this->randomString();
    $node = Node::create([
      'title' => $title,
      'type' => $node_type->id(),
    ]);

    // Module's hooks are available but not invoked.
    // Hence, hook_entity_presave effect is unavailable.
    // Will see hook_entity_presave impact in Functional test examples.
    $this->assertEquals($title, $node->getTitle());
  }

}
