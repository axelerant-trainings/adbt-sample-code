<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fitness_challenge\Service\WorkoutChallengeService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;

/**
 * Provides a form for users to submit their workout details.
 */
class WorkoutForm extends FormBase {

  
  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * The workout challenge service object.
   *
   * @var \Drupal\fitness_challenge\WorkoutChallengeService
   */
  protected $workoutChallenge;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a ChallengeForm object.
   *
   * @param \Drupal\fitness_challenge\WorkoutChallengeService
   *   The workout challenge service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(WorkoutChallengeService $workout_challenge, EntityTypeManagerInterface $entity_type_manager, CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->workoutChallenge = $workout_challenge;
    $this->entityTypeManager = $entity_type_manager;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('fitness_challenge.challenge_service'),
        $container->get('entity_type.manager'),
        $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Returns a unique identifier for the workout form.
    return 'fitness_challenge_workout_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Image upload field.
    $form['workout_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Workout Image'),
      '#upload_location' => 'public://workout-uploads/',
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'jpg jpeg'],
        'FileSizeLimit' => ['fileLimit' => 100000],
      ],
      '#required' => FALSE,
      '#cardinality' => 1,
      '#field_name' => 'workout_image',
    ];

    // Define a select field for the type of workout.
    $form['workout_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Workout Type'),
      '#options' => [
        'cardio' => $this->t('Cardio'),
        'strength' => $this->t('Strength'),
        'flexibility' => $this->t('Flexibility'),
        'balance' => $this->t('Balance'),
      ],
      '#ajax' => [
        'callback' => '::updatePreviousWorkoutDuration',
        'event' => 'change',
        'wrapper' => 'previous-workout-duration-wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Fetching previous workout duration...'),
        ],
      ],
      '#required' => TRUE, // Make this field required.
    ];
    
    // Placeholder for previous workout duration.
    $form['previous_workout_duration'] = [
      '#type' => 'markup',
      '#prefix'=> '<div id="previous-workout-duration-wrapper">',
      '#suffix'=> '</div>',
      '#markup' => '',
    ];

    // Define a number field for the duration of the workout in minutes.
    $form['duration'] = [
      '#type' => 'number',
      '#title' => $this->t('Workout Duration (in minutes)'),
      '#min' => 1, // Set a minimum value of 1 minute.
      '#required' => TRUE, // Make this field required.
    ];

    // Define a date field for the workout date.
    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Workout Date'),
      '#required' => TRUE, // Make this field required.
    ];

    // Define the submit button for the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Workout'),
    ];

    // Return the complete form array.
    return $form;
  }



  /**
   * AJAX callback to update the previous workout duration.
   *
   * This method retrieves and updates the previous workout duration based on
   * the selected workout type from the form.
   *
   * @param array &$form
   *   The form array containing the form elements.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   A renderable array containing the updated markup for the previous workout duration.
   */
  public function updatePreviousWorkoutDuration(array &$form, FormStateInterface $form_state) {
    
    // Get the selected workout type from the form state.
    $workout_type = $form_state->getValue('workout_type');
    
    // Fetch the previous workout duration for the selected workout type.
    $previous_duration = $this->workoutChallenge->getPreviousWorktypeLoggedTime($workout_type);

    // Initialize the markup variable to store the message.
    $markup = '';

    // Check if a previous duration was found; if so, create the markup message.
    if ($previous_duration !== FALSE) {
      $markup = $this->formatPlural(
        $previous_duration,
        'Previous workout duration for %type was <strong>@duration</strong> minute.', 
        'Previous workout duration for %type was <strong>@duration</strong> minutes.', 
        [
        '%type' => $workout_type, // Placeholder for the workout type.
        '@duration' => $previous_duration, // Placeholder for the previous duration.
      ]);
    }

    // Update the form element with the new markup.
    $form['previous_workout_duration']['#markup'] = $markup;

    // Return the renderable array for the updated previous workout duration.
    return $form['previous_workout_duration'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the ID of the current user.
    $uid = \Drupal::currentUser()->id();

    $tags = ['fitness_challenge_leaderboard'];
	  $this->cacheTagsInvalidator->invalidateTags($tags);
	
	  $this->messenger()->addMessage($this->t('Tags %tags got invalidated successfully.', [
	    '%tags' => implode(', ', $tags),
	  ]));
    
    // Retrieve the uploaded file ID from the form state.
    $file_id = $form_state->getValue('workout_image') 
    ? $form_state->getValue('workout_image')[0] 
    : NULL;
  
    // Load the file entity based on the file ID.
    $file = $file_id 
    ? $this->entityTypeManager->getStorage('file')->load($file_id) 
    : NULL;
    
    if ($file) {
      $file->setPermanent();
      $file->save();
      
      $message = $this->t(
        'Workout image uploaded successfully, @link to view it.', 
        ['@link' => $this->workoutChallenge->convertUriToLinkObj($file->getFileUri(), 'click')->toString()], 
        ['html' => TRUE]
      );

      $this->messenger()->addMessage($message);
    }
    else {
      $file_id = NULL;
    }

	  // Insert the workout details into the database.
	  $this->workoutChallenge->logWorkout(
	    $uid, 
	    $form_state->getValue('workout_type'), 
	    $form_state->getValue('duration'),
	    strtotime($form_state->getValue('date')),
      $file_id
	  );

    // Display a message to the user confirming the workout has been recorded.
    \Drupal::messenger()->addMessage($this->t('Workout has been recorded successfully.'));
  }

}
