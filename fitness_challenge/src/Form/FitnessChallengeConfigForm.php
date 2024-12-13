<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides a configuration form for the Fitness Challenge settings.
 */
class FitnessChallengeConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // Returns the editable configuration names for this form.
    return ['fitness_challenge.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Returns a unique identifier for the settings form.
    return 'fitness_challenge_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load the existing configuration for the fitness challenge settings.
    $config = $this->config('fitness_challenge.settings');

    // Select Challenge Level.
    $form['challenge_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Fitness Challenge Title'),
      '#default_value' => $config->get('challenge_title'), // Set the default value from the configuration.
    ];

    // Select Challenge Level.
    $form['challenge_level'] = [
      '#type' => 'select',
      '#title' => $this->t('Next Fitness Challenge Difficulty Level'),
      '#options' => [
        'easy' => $this->t('Easy'),
        'medium' => $this->t('Medium'),
        'hard' => $this->t('Hard'),
      ],
      '#default_value' => $config->get('challenge_level'), // Set the default value from the configuration.
    ];

    // Add a datetime field to the form.
    $form['challenge_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Next Fitness Challenge Date'),
      '#description' => $this->t('Select the date and time for the next fitness challenge.'),
      '#default_value' => $config->get('challenge_date') 
        ? DrupalDateTime::createFromTimestamp($config->get('challenge_date')) 
        : null,
    ];

    // Set List of nodes with success stories to ignore or hide.
    $form['ignore_stories'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ignore Success Stories'),
      '#default_value' => $config->get('ignore_stories'), // Set the default value from the configuration.
      '#description' => $this->t('Pass comma separated node ids to ignore or hide.'),  // Sharing hint about the expected input.
    ];

    // Build the form using the parent class's buildForm method.
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the configuration settings from the form.
    $this->configFactory->getEditable('fitness_challenge.settings')
      ->set('challenge_title', $form_state->getValue('challenge_title'))
      ->set('challenge_level', $form_state->getValue('challenge_level'))
      ->set('challenge_date', $form_state->getValue('challenge_date')->getTimestamp())
      ->set('ignore_stories', $form_state->getValue('ignore_stories'))
      ->save();

    // Call the parent submitForm method to ensure any additional functionality is executed.
    parent::submitForm($form, $form_state);
  }
}
