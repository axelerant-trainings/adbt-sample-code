<?php

namespace Drupal\adbt_testing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides AjaxShowMySelectionForm form.
 */
class AjaxShowMySelectionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ajax_show_selection_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['numbers'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Number'),
      '#options' => [
        '1' => $this->t('One'),
        '2' => $this->t('Two'),
        '3' => $this->t('Three'),
        '4' => $this->t('Four'),
      ],
      '#ajax' => [
        'callback' => '::myAjaxCallback',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-output',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Please wait...'),
        ],
      ],
    ];

    // A markup that will be updated
    // when the user selects an item from the select box above.
    $form['output'] = [
      '#markup' => '',
      '#prefix' => '<div id="edit-output">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Ajax callback to show selected favourite number.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form output element.
   */
  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
    if ($selectedValue = $form_state->getValue('numbers')) {
      $selectedText = $form['numbers']['#options'][$selectedValue];
      $form['output']['#markup'] = '<h2>Your favourite number is <i>' . $selectedText . '</i></h2>';
    }

    // Return the prepared textfield.
    return $form['output'];
  }

}
