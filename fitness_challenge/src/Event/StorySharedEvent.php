<?php

namespace Drupal\fitness_challenge\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\node\NodeInterface;

/**
 * Defines an event to dispatch when a story is shared.
 */
class StorySharedEvent extends Event {

  /**
   * The node object.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * The creation timestamp.
   *
   * @var int
   */
  protected $wordCount;

  /**
   * Constructs the event.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node object.
   * @param int $wordCount
   *   Total words shared in the story.
   */
  public function __construct(NodeInterface $node, $word_count) {
    $this->node = $node;
    $this->wordCount = $word_count;
  }

  /**
   * Get the node object.
   *
   * @return Drupal\node\NodeInterface
   *   The node object.
   */
  public function getNode() {
    return $this->node;
  }

  /**
   * Get the story word count.
   *
   * @return int
   *   The word count value.
   */
  public function getWordCount() {
    return $this->wordCount;
  }

}
