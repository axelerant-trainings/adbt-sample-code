<?php

namespace Drupal\fitness_challenge\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

use Drupal\Core\State\StateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\File\FileUrlGenerator;

/**
 * Provides a service for managing fitness challenges and workouts.
 */
class WorkoutChallengeService {


  use StringTranslationTrait;

  /**
   * The File URL Generator service.
   *
   * @var \Drupal\Core\File\FileUrlGenerator
   */
  protected $fileUrlGenerator;

    /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

    /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a WorkoutChallengeService instance.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(Connection $database, LoggerChannelFactoryInterface $logger_factory, StateInterface $state, AccountProxyInterface $current_user, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, FileUrlGenerator $file_url_generator) {

    $this->state = $state;
    $this->currentUser = $current_user;
    $this->database = $database; // Store the database connection for later use.
    $this->loggerFactory = $logger_factory; // Store the logger service for logging events.
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }


  /**
   * Converts a file URI to a clickable link object.
   *
   * @param string $file_uri
   *   The file URI (e.g., 'public://example.png').
   * @param string $link_text
   *   The text for the clickable link (e.g., 'Download File').
   *
   * @return Drupal\Core\Link
   *   A rendered HTML link as a string.
   */
  public function convertUriToLinkObj(string $file_uri, string $link_text = 'this link') {
    // Generate the public URL for the file.
    $file_url = $this->fileUrlGenerator->generateAbsoluteString($file_uri);

    // Create the URL object with 'external' set to TRUE.
    $url = Url::fromUri($file_url, ['external' => TRUE]);

    // Create the link object using the URL and link text.
    return Link::fromTextAndUrl($this->t($link_text), $url);

  }
  
  /**
   * Retrieves nodes with success stories for the current user, excluding specified nodes.
   *
   * @param string|null $search_title
   *   (optional) The title to search for in the success story nodes. 
   *   If provided, only nodes with matching titles will be retrieved.
   *
   * @return \Drupal\node\NodeInterface[]
   *   An array of node entities representing the user's success stories.
   */
  public function getUserSuccessStories(string $search_title = NULL) {
    // Load the configuration that stores the excluded node IDs.
    $config = $this->configFactory->get('fitness_challenge.settings');
    $ignore_stories = $config->get('ignore_stories');

    // Convert the comma-separated string to an array of integers.
    $ignore_stories_array = [];
    if (!empty($ignore_stories)) {
      // Split the string by commas, trim whitespace, and store in an array.
      $ignore_stories_array = array_map('trim', explode(',', $ignore_stories));
    }

    // Get the node storage object for loading and querying nodes.
    $node_storage = $this->entityTypeManager->getStorage('node');

    // Build the entity query for nodes.
    $query = $node_storage->getQuery()
      ->condition('uid', $this->currentUser->id()) // Filter nodes by the current user's UID.
      // ->condition('status', 1, '<>') // Uncomment to include only published and not unpublished nodes.
      ->condition('type', 'page') // Example: fetching nodes of type 'page'.
      ->sort('created', 'DESC') // Sort by creation date in descending order (latest first).
      ->accessCheck(TRUE); // Ensure access checking is enabled to restrict results based on permissions.

    // Exclude the nodes from the query if we have node IDs to exclude.
    if (!empty($ignore_stories_array)) {
      // Apply a condition to exclude specified node IDs.
      $query->condition('nid', $ignore_stories_array, 'NOT IN');
    }

    // Filter results based on the search title if provided.
    if ($search_title) {
      // Apply a condition to include nodes with titles matching the search term.
      $query->condition('title', '%' . $search_title . '%', 'LIKE');
    }

    // Execute the query and get an array of node IDs.
    $nids = $query->execute();

    // Load and return the node entities based on the retrieved node IDs.
    return $node_storage->loadMultiple($nids);
  }

  /**
   * Get Previous Worktype Logged Time.
   *
   * @param string $workout_type
   *   The type of workout (e.g., cardio, strength).
   */
  public function getPreviousWorktypeLoggedTime(string $workout_type) {
    $uid = $this->currentUser->id();

    // Query the database for the most recent workout duration for the given user and workout type.
    $query = $this->database->select('fitness_workouts', 'fw')
      ->fields('fw', ['duration'])
      ->condition('fw.uid', $uid, '=')
      ->condition('fw.workout_type', $workout_type, '=')
      ->orderBy('fw.date', 'DESC')
      ->range(0, 1); // Limit to the most recent workout.

    // Fetch the result.
    return $query->execute()->fetchField();
  }

  	/**
	 * Sets the punch in timestamp for the current user in the state.
	 *
	 * @param int $time
	 *   The timestamp to be set as the last workout time for the current user.
	 */
	public function setUserPunchInTime(int $time) {
	  $uid = $this->currentUser->id();
	  // Store the provided timestamp for the current user's last workout.
	  $this->state->set('fitness_punch_in_time_' . $uid, $time);
	}

  /**
   * Retrieves the punch in timestamp for the current user from the state.
   *
   * @return int|null
   *   The timestamp of the last workout, or NULL if not set.
   */
  public function getUserPunchInTime() {
    $uid = $this->currentUser->id();
    // Retrieve the last workout timestamp for the current user.
    return $this->state->get('fitness_punch_in_time_' . $uid);
  }

  
  /**
   * Logs a workout into the fitness_workouts table.
   *
   * This method records a user's workout type and duration in the database.
   *
   * @param int $user_id
   *   The ID of the user logging the workout.
   * @param string $workout_type
   *   The type of workout (e.g., cardio, strength).
   * @param int $duration
   *   The duration of the workout in minutes.
   * @param int $timestamp
   *   The date timestamp of the workout details.
   */
  public function logWorkout(int $user_id, string $workout_type, int $duration, int $timestamp, int|NULL $image_fid): void {
  
      // Insert the workout record into the database.
    $this->database->insert('fitness_workouts')
      ->fields([
        'uid' => $user_id, // User ID.
        'workout_type' => $workout_type, // Type of workout.
        'duration' => $duration, // Duration of the workout.
        'date' => $timestamp, // Timestamp of when the workout was logged.
        'image_fid' => $image_fid,
      ])
      ->execute(); // Execute the insert query.
      
    // Log warning message for the workout details if duration is more than 2 hours.
    if ($duration > 120) {
        $this->loggerFactory->get('fitness_challenge')->warning('User {user_id} logged a {workout_type} workout of {duration} minutes.', [
          'user_id' => $user_id,
          'workout_type' => $workout_type,
          'duration' => $duration,
        ]);
    }
  }

}
