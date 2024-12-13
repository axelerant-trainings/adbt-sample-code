<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for users to submit their fitness goals.
 */
class FitnessGoalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Returns a unique identifier for the form.
    return 'fitness_goal_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Define a text field for users to enter their fitness goals.
    $form['fitness_goal'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fitness Goal'),
      '#description' => $this->t('Enter your main fitness goal.'),
      '#required' => TRUE,
    ];
    
    $form['copy'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send me a copy'),
    ];

    // Define a submit button for the form.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Goal'),
    ];

    // Return the complete form array.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get the value entered by the user.
    $fitness_goal = $form_state->getValue('fitness_goal');
    
    // Check if the goal is at least 5 characters long.
    if (strlen($fitness_goal) < 5) {
      // Set an error message on the 'fitness_goal' field.
      $form_state->setErrorByName('fitness_goal', $this->t('The fitness goal must be at least 5 characters long.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Process the form submission and display the user's fitness goal.
    // $this->messenger() is also an alternate here.
    \Drupal::messenger()->addMessage($this->t('Your fitness goal is: @goal', [
      '@goal' => $form_state->getValue('fitness_goal'),
    ]));
  }

}
