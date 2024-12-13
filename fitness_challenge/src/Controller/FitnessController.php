<?php

namespace Drupal\fitness_challenge\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;

/**
 * Provides a controller class for the ultimate fitness challenge page.
 */
class FitnessController extends ControllerBase {

  /**
   * Builds the fitness challenge page.
   *
   * @return array
   *   A renderable array containing the markup for the page.
   */
  public function challengePage() {

    // Add a modal link for "Submit My Success Story".
    $modal_link = [
      '#type' => 'link',
      '#title' => $this->t('Submit My Success Story'),
      '#url' => Url::fromRoute('fitness_challenge.modal_form'),  // Define the route for the modal form.
      '#attributes' => [
        'class' => ['use-ajax'],  // Enable AJAX for the link.
        'data-dialog-type' => 'modal',  // Specify that the dialog type is a modal.
        'data-dialog-options' => json_encode(['width' => 700]),  // Optional: specify modal options like width.
      ],
      '#attached' => [
        'library' => [
            'core/drupal.dialog.ajax',  // Attach the necessary core library for AJAX modals.
        ],
      ],
    ];


    // The welcome message.
    $welcome_markup = [
      '#markup' => '<p>Welcome! Let’s transform ourselves with daily workouts, motivation, and community support in the ultimate fitness challenge.</p>',
      '#cache' => [
        // 'max-age' => 20,
        // 'contexts' => ['url.query_args:category'],
        'tags' => ['config:fitness_challenge.settings'],
      ],
    ];

    // The next challenge detail from configurations.
    $config = \Drupal::configFactory()->get('fitness_challenge.settings');
    $next_challenge = [
      '#markup' => $this->t('<p>Next challenge <strong>@title</strong> of dificulty %level will roll out on @date. Stay prepared!</p>', [
        '@title' => $config->get('challenge_title'),
        '%level' => $config->get('challenge_level'),
        '@date' => DrupalDateTime::createFromTimestamp($config->get('challenge_date'))->format('d M Y - H:i'),
      ]),
    ];

    return [
      $modal_link,
      $welcome_markup,
      $next_challenge,
    ];

  }
}
