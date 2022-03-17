<?php

namespace Drupal\Tests\adbt_testing\Unit;

/**
 * Dummy data to support mocking.
 */
class DummyData {

  /**
   * Variable to support dynamic fields.
   *
   * @var array
   */
  private $field = [];

  /**
   * Set field value.
   *
   * @param string $name
   *   The field name.
   * @param mixed $value
   *   The field value.
   */
  public function set($name, $value) {
    $this->field[$name] = $value;
  }

  /**
   * Retrieves field value.
   *
   * @param string $name
   *   The field name.
   *
   * @return mixed
   *   The field value.
   */
  public function get($name) {
    if (isset($this->field[$name])) {
      return $this->field[$name];
    }
    else {
      return NULL;
    }
  }

}
