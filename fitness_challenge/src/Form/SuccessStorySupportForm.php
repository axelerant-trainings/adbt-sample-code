<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SuccessStorySupportForm.
 */
class SuccessStorySupportForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs the form.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'success_story_support_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Autocomplete textfield for success story titles.
    $form['success_story'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Success Story Title'),
      '#autocomplete_route_name' => 'fitness_challenge.autocomplete',
      '#description' => $this->t('Type the title of your success story to get support.'),
    ];

    // Text area for message to admin.
    $form['message_to_admin'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message to Admin'),
      '#description' => $this->t('Write a message to the admin regarding your success story.'),
      '#required' => TRUE, // Optional field.
    ];

    // Submit button.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Support Request'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $story_id = $form_state->getValue('success_story');
    $admin_message = $form_state->getValue('message_to_admin');

    // Display a success message to the user.
    // $this->messenger()->addMessage($this->t('Form submitted for success story id %story, with message to admin \'@message\'', [
    //   '%story' => $story_id,
    //   '@message' => $admin_message,
    // ]));

    // Redirect to the confirmation page, passing the node ID and message as query parameters.
    $form_state->setRedirect('fitness_notifications.confirm_support_request', ['node' => $story_id], ['query' => ['admin_message' => $admin_message]]);

  }

}