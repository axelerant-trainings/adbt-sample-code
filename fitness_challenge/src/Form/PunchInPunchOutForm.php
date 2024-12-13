<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fitness_challenge\Service\WorkoutChallengeService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form with Punch In and Punch Out buttons.
 */
class PunchInPunchOutForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'punch_in_punch_out_form';
  }


  /**
   * The workout challenge service object.
   *
   * @var \Drupal\fitness_challenge\WorkoutChallengeService
   */
  protected $workoutChallenge;

  /**
   * Constructs a PunchInPunchOutForm object.
   *
   * @param \Drupal\fitness_challenge\WorkoutChallengeService
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
   * Build the form with two buttons: Punch In and Punch Out.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {


    $last_workout_time = $this->workoutChallenge->getUserPunchInTime();

		// Attach settings for the Fitness Challenge module to the form.
		$form['#attached']['drupalSettings']['fitness_challenge']['user_punch'] = [
		  // The display name of the current user.
		  'username' => $this->currentUser()->getDisplayName(),
		
		  // The timestamp of the user's last workout.
		  'lastpunch' => $last_workout_time,
		];

    // Craft the message to show to the user.
    $available = time() - $last_workout_time;
    if ($last_workout_time && $available) {
      $content = $this->t('You are available since @minutes minutes.', [
        '@minutes' => round($available/60, 1),
      ]);
    } else {
      $content = $this->t('Start your workout challenge today!');
    }

    $form['markup'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $content . '</p>',
    ];


    // Add Punch In button.
    $form['punch_in'] = [
      '#type' => 'submit',
      '#value' => $this->t('Punch In'),
      '#name' => 'punch_in',
      '#attributes' => [
        'class' => ['button--primary']
      ],
      '#access' => !($last_workout_time && $available)
    ];

    // Add Punch Out button.
    $form['punch_out'] = [
      '#type' => 'submit',
      '#value' => $this->t('Punch Out'),
      '#name' => 'punch_out',
      '#access' => $last_workout_time && $available
    ];

    return $form;
  }

  /**
   * Handle form submission.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the triggering button.
    $trigger = $form_state->getTriggeringElement()['#name'];

    if ($trigger === 'punch_in') {
      $this->workoutChallenge->setUserPunchInTime(time());
    }
    elseif ($trigger === 'punch_out') {
      $this->workoutChallenge->setUserPunchInTime(0);
    }

  }
}
