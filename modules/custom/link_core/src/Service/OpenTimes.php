<?php

namespace Drupal\link_core\Service;

use Drupal\Core\Database\Connection;

/**
 * Returns list of opening times for location for service type, keyed by service id
 *
 * @ingroup link_core
 */
class OpenTimes {

  /**
   * Database service
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  private $database;

  /**
   * Constructor
   *
   * @param \Drupal\Core\Database\Driver\mysql\Connection $database
   */
  public function __construct(Connection $database) {
    $this->database = $database;
    $this->buildList();
  }

  /**
   * Builds opening times list
   */
  private function buildList(){
    $this->openTimesList = ['meow'];
  }

  /**
   * Get list of opening times
   *
   * @return array $this->openTimesList
   */
  public function getList(){
    return $this->openTimesList;
  }

}
