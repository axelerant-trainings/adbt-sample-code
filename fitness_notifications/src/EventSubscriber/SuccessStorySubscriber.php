<?php

namespace Drupal\fitness_notifications\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\fitness_challenge\Event\StorySharedEvent;
use Drupal\fitness_challenge\Event\FitnessChallengeEvents;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Event subscriber to handle fitness challenge node creation events.
 */
class SuccessStorySubscriber implements EventSubscriberInterface {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The logger factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs the event subscriber.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   */
  public function __construct(DateFormatterInterface $date_formatter, LoggerChannelFactoryInterface $logger_factory) {
    $this->dateFormatter = $date_formatter;
    $this->loggerFactory = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      FitnessChallengeEvents::STORY_SHARED => 'onSharingStory',
    ];
  }
  
  /**
   * React to the story shared event.
   *
   * @param \Drupal\fitness_challenge\Event\StorySharedEvent $event
   *   The event object.
   */
  public function onSharingStory(StorySharedEvent $event) {
    $node = $event->getNode();

    // Format the creation timestamp using the injected date formatter.
    // using 'short' date format defined at /admin/config/regional/date-time.
    $formatted_date = $this->dateFormatter->format($node->getCreatedTime(), 'short');

    // Simply, logging information about the created node.
    $this->loggerFactory->get('fitness_notifications')->info('Fitness Challenge: Node created by user @user with title @title at @time.', [
      '@user' => $node->getOwnerId(),
      '@title' => $node->getTitle(),
      '@time' => $formatted_date,
    ]);
  }

}
