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
    // Get schedule days and times, keyed by service ID.
    $this->database->query("SET SQL_MODE=''");
    $query = $this->database->select('node__field_schedule', 's');
    $query->join('paragraph__field_day', 'd', 's.field_schedule_target_id = d.entity_id');
    $query->join('paragraph__field_time', 't', 's.field_schedule_target_id = t.entity_id');
    $result = $query
      ->fields('s', ['entity_id'])
      ->fields('d', ['field_day_value'])
      ->fields('t', ['field_time_from', 'field_time_to'])
      ->groupBy('s.entity_id')
      ->groupBy('d.field_day_value')
      ->execute()
      ->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_ASSOC);
    $this->openTimesList = $result;
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
