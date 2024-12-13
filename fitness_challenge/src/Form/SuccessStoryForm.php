<?php

namespace Drupal\fitness_challenge\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\fitness_challenge\Event\FitnessChallengeEvents;
use Drupal\fitness_challenge\Event\StorySharedEvent;

/**
 * Modal form for submitting a success story.
 */
class SuccessStoryForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;
  


  /**
   * Constructs a SuccessStoryForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service to handle events in the application.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher) {
    $this->entityTypeManager = $entity_type_manager;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'success_story_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    // Add a wrapper div around the entire form section for easy replacement.
    $form['#prefix'] = '<div id="success-story-form-wrapper">';
    $form['#suffix'] = '</div>';

    $form['success_story'] = [
      '#type' => 'textarea',
      // Let's ignore field title/label.
      // '#title' => $this->t('Write your story'),
      '#required' => TRUE,
      '#name' =>'success_story',
      '#description' => $this->t('Write your story and click submit.'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::ajaxSubmitForm',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * AJAX callback to handle the form submission.
   */
  public function ajaxSubmitForm(array &$form, FormStateInterface $form_state) {

    // Use the entity type manager service to create the node.
    $node_storage = $this->entityTypeManager->getStorage('node');
    
    // Create a new Basic Page node.
    $story = $form_state->getValue('success_story');
    $node = $node_storage->create([
      'type' => 'page',  // Machine name of "Basic Page" content type.
      'title' => substr($story, 0, 20),
      'body' => [
        'value' => $story,
        'format' => 'basic_html',
      ],
      'status' => FALSE,
      'uid' => $this->currentUser()->id(),
    ]);
    $node->save();

    // Dispatch the event.
    $event = new StorySharedEvent($node, str_word_count($story));
    $this->eventDispatcher->dispatch($event, FitnessChallengeEvents::STORY_SHARED);

    $this->logger('fitness_challenge')->info($this->t("Event @event dispatched for node @nid", [
      "@event" => FitnessChallengeEvents::STORY_SHARED,
      "@nid" => $node->id(),
    ]));

    // Prepare an AjaxResponse to return.
    $response = new AjaxResponse();

    // Replace the entire form with the success message.
    $success_message = '<div>' . $this->t('Thank you for sharing your story, site admin will review and publish it soon.') ;
    $response->addCommand(new ReplaceCommand('#success-story-form-wrapper', $success_message));

    return $response;
  }

}
