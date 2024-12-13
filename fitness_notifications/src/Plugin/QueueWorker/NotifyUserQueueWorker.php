<?php

namespace Drupal\fitness_notifications\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @QueueWorker(
 *   id = "notify_user_queue_worker",
 *   title = @Translation("Notify User Queue Worker"),
 *   cron = {"time" = 60}
 * )
 */
class NotifyUserQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface{

  /**
   * The logger factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructs a new NotifyUserQueueWorker.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $logger_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')
    );
  }

  /**
   * Processes a single queue item.
   *
   * @param mixed $data
   *   The data that was passed to the queue.
   */
  public function processItem($data) {
    // Retrieve the logger for the 'fitness_notifications' channel.
    $logger = $this->loggerFactory->get('fitness_notifications');

    // Log the processing of each item.
    $logger->info('Notification(node @node_id) preparing for the user @user_id.', [
      '@node_id' => $data['node_id'],
      '@user_id' => $data['user_id'],
    ]);

    // Simulate a delay (10 seconds) for each item.
    sleep(10);

    // Log when the processing of the item is complete.
    $logger->info('Notification successfully sent to user @user_id. Completed @position/@total.', [
      '@user_id' => $data['user_id'],
      '@position' => $data['position'],
      '@total' => $data['total'],
    ]);
  }

}