<?php

namespace Drupal\fitness_notifications\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\fitness_notifications\Service\NotificationService;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

/**
 * Class ConfirmSupportRequestForm.
 *
 * Provides a confirmation form for sending support request notifications.
 */
class ConfirmSupportRequestForm extends ConfirmFormBase {

  /**
   * The node object.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * The notification service.
   *
   * @var \Drupal\fitness_notifications\Service\NotificationService
   */
  protected $notificationService;

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The user storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $userStorage;

  /**
   * Constructs a new ConfirmSupportRequestForm.
   *
   * @param \Drupal\fitness_notifications\Service\NotificationService $notification_service
   *   The notification service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(NotificationService $notification_service, RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    $this->notificationService = $notification_service;
    $this->requestStack = $request_stack;
    $this->userStorage = $entity_type_manager->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('fitness_notifications.notification_service'),
      $container->get('request_stack'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'confirm_support_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to send the support request?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    // Redirect to the homepage or wherever appropriate.
    return Url::fromRoute('fitness_challenge.submitted_stories_support_form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Confirm');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL) {
    // Store node and message from query parameters.
    $this->node = $node;

    // Get the message from the query parameter using the request stack.
    $message = $this->requestStack->getCurrentRequest()
      ->query
      ->get('admin_message');

    // Display the node title and admin message.
    $form['node_title'] = [
      '#markup' => $this->t('<p><strong>Node</strong>: @title</p>', ['@title' => $node->getTitle()]),
    ];
    $form['admin_message'] = [
      '#markup' => $this->t('<p><strong>Message to admin</strong>: @message</p>', ['@message' => $message ?? '-']),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Use dependency injection to load the admin user (UID 1).
    $admin_user = $this->userStorage->load(1);

    // Prepare the support request details.
    $story_details = [
      'node' => $this->node,
      'admin_message' => $this->requestStack->getCurrentRequest()->query->get('admin_message'),
    ];

    // Send the notification using the injected notification service.
    $this->notificationService->sendNotification($admin_user, $story_details, 'support_request');

    // Add a message to confirm the action.
    $this->messenger()->addMessage($this->t('The support request notification has been sent.'));

    // Redirect to the homepage after confirmation.
    $form_state->setRedirect('<front>');
  }

}
