<?php

namespace Drupal\fitness_notifications\Service;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Url;
use Drupal\user\UserInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class NotificationService.
 *
 * Handles email notifications for Success Stories.
 */
class NotificationService {

  use StringTranslationTrait;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The logger factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new NotificationService object.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager service for sending emails.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   */
  public function __construct(MailManagerInterface $mail_manager, LoggerChannelFactoryInterface $logger_factory) {
    $this->mailManager = $mail_manager; // Store the mail manager service.
    $this->loggerFactory = $logger_factory; // Store the logger service.
  }

  /**
   * Sends email notifications to users.
   *
   * This method sends notifications based on the specified type
   * ('published' or 'submitted') for the given node.
   *
   * @param \Drupal\user\UserInterface $recipient
   *   The user to receive the email notification.
   * @param array $story_details
   *   The details of the created story that triggered the notification.
   * @param string $type
   *   The type of notification ('published' or 'submitted').
   */
  public function sendNotification(UserInterface $recipient, array $story_details, string $type) {
    $module = 'fitness_notifications'; // The module providing the notification.
    $key = $type; // The key representing the type of notification.
    $to = $recipient->getEmail(); // Get the recipient's email address.
    $langcode = $recipient->getPreferredLangcode(); // Get the recipient's preferred language.
    $send = TRUE; // Flag indicating whether to send the email.

		$params = [];
    $node = $story_details['node'];
    
    // Prepare the email message based on the notification type.
    $params['short_title'] = substr($node->getTitle(), 0, 25);

    // Create a URL to the node.
    $params['node_url'] = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE])->toString();

    if ($type === 'support_request') {
      $params['author_name'] = $node->getOwner()->getDisplayName();
      $params['admin_message'] = $story_details['admin_message'];
    }

    // Send the email using the mail manager service.
    $result = $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    // Check the result and log success or error messages.
    if ($result['result'] !== TRUE) {
      $this->loggerFactory->get('fitness_notifications')->error('There was a problem sending the email notification to %recipient.', ['%recipient' => $recipient->getEmail()]);
    }
    else {
      $this->loggerFactory->get('fitness_notifications')->notice('Notification sent to %recipient for %node.', [
        '%recipient' => $recipient->getEmail(),
        '%node' => $node->getTitle(),
      ]);
    }
  }
}
