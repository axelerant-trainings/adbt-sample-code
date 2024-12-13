<?php

namespace Drupal\fitness_challenge\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Workout Leaderboard' Block.
 *
 * @Block(
 *   id = "workout_leaderboard_block",
 *   admin_label = @Translation("Workout Leaderboard"),
 *   category = @Translation("Custom")
 * )
 */
class WorkoutLeaderboardBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Build the render array for the block.
    return [
      '#markup' => $this->generateLeaderboard(), // Generate and return the leaderboard content.
      '#cache' => [
        'tags' => ['fitness_challenge_leaderboard'],
      ],
    ];
  }

  /**
   * Generates the leaderboard content.
   *
   * This method retrieves workout data from the database and creates the HTML markup for the leaderboard.
   *
   * @return string
   *   The HTML markup for the leaderboard.
   */
  protected function generateLeaderboard(): string {
    // Query the database for the workout records to generate the leaderboard.
    $query = \Drupal::database()->select('fitness_workouts', 'fw');
    $query->addExpression('SUM(duration)', 'total_duration'); // Calculate the total duration for each user.
    $query->fields('fw', ['uid', 'workout_type', 'duration'])
      ->groupBy('uid')
      ->orderBy('total_duration', 'DESC')
      ->range(0, 10);

    $results = $query->execute()->fetchAll();

    // Start building the leaderboard output.
    $output = '<ul>';

    // Iterate over the results and build the list items.
    foreach ($results as $record) {
      // Fetch user information (this assumes user names are stored in the users table).
      $account = \Drupal::entityTypeManager()->getStorage('user')->load($record->uid);
      $username = $account ? $account->getDisplayName() : $this->t('Unknown User');

      $output .= '<li>' . $username . ' - ' . $record->total_duration . ' Points</li>';
    }

    $output .= '</ul>';
    
    // Return the generated output.
    return $output;
  }

}
