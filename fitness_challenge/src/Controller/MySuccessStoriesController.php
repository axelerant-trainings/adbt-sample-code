<?php

namespace Drupal\fitness_challenge\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\fitness_challenge\Service\WorkoutChallengeService;

/**
 * Controller to display a table of nodes, sorted by latest first.
 */
class MySuccessStoriesController extends ControllerBase {

  /**
   * The workout challenge service object.
   *
   * @var \Drupal\fitness_challenge\Service\WorkoutChallengeService
   */
  protected $workoutChallenge;

  /**
   * Constructs a PunchInPunchOutForm object.
   *
   * @param \Drupal\fitness_challenge\Service\WorkoutChallengeService
   *   The workout challenge service.
   */
  public function __construct(WorkoutChallengeService $workout_challenge) {
    $this->workoutChallenge = $workout_challenge;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('fitness_challenge.challenge_service')
    );
  }

  /**
   * Displays a table listing stories submitted by the user.
   */
  public function storyTable() {
    // Collect user succes stories.
    $nodes = $this->workoutChallenge->getUserSuccessStories();

    // Build the rows for the table.
    $rows = [];
    foreach ($nodes as $node) {
      // Create a link to the node.
      $link = Link::fromTextAndUrl($node->getTitle(), Url::fromRoute('entity.node.canonical', ['node' => $node->id()]))->toString();

      // Determine the node's publish status.
      $status = $node->isPublished() ? $this->t('Approved') : $this->t('Pending Approval');

      // Add a row to the table.
      $rows[] = [
        'title' => ['data' => $link],
        'status' => ['data' => $status],
      ];
    }

    // Return the render array for the table.
    return [
      '#type' => 'table',
      '#header' => [
        'title' => $this->t('Story'),
        'status' => $this->t('Status'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No story available.'),
      '#attributes' => [
        // Apply the "table" class for proper styling in Olivero.
        'class' => [
          'table',
          'draggable-table',
        ], 
        'id' => 'custom-node-table',
      ],
    ];
  }

}