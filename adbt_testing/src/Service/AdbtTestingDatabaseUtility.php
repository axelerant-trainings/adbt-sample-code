<?php

namespace Drupal\adbt_testing\Service;

use Drupal\Core\Database\Connection;

/**
 * Defines ADBT testing databse utility service.
 */
class AdbtTestingDatabaseUtility {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * ConfigCommands constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Submit user testinomial.
   *
   * @param int $uid
   *   User id.
   * @param string $input
   *   Testimonial input.
   */
  public function addTestimonial(int $uid, $input) {
    return $this->database->insert('adbt_testing_testimonial')
      ->fields([
        'uid' => $uid,
        'testimonial' => $input,
      ])
      ->execute();
  }

  /**
   * Retrieves user testinomial.
   *
   * @param int|null $uid
   *   User id.
   */
  public function getUserTestimonial($uid = NULL) {
    $query = $this->database->select('adbt_testing_testimonial', 'att')
      ->fields('att');

    if ($uid) {
      $query->condition('uid', $uid);
    }

    return $query->execute()->fetchAll();
  }

}
