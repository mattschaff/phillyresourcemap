<?php

namespace Drupal\link_core\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * @ViewsFilter("open_now_filter")
 */
class OpenNowFilter extends FilterPluginBase {
  /**
   * Database service
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  private $database;

  /**
   * Constructor
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param [type] $plugin_definition
   * @param \Drupal\Core\Database\Driver\mysql\Connection $database
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function query() {
    // Get current day and time, paying respect to timezone.
      // Set appropriate timezone.
      $config = \Drupal::config('system.date');
      $config_data_default_timezone = $config->get('timezone.default');
      date_default_timezone_set($config_data_default_timezone);
      // Time at midnight today
      $time = time();
      $time_at_midnight = $time - ($time - strtotime("today"));
      // Current day.
      $now_day_of_week = date('w');
      // Seconds since midnight.
      $seconds_since_midnight = $time - $time_at_midnight;

    // Run Open Now query.
      // Get paragraph IDs where field day is today.
      // Join inner w/ paragraph IDs where field time from is before and time to is after time.
      // Then get service NIDs w/ these paragraph IDs.
      $query = $this->database->select('paragraph__field_day', 'd');
      $query->join('paragraph__field_time', 't', 't.entity_id = d.entity_id');
      $query->join('node__field_schedule', 's', 's.field_schedule_target_id = t.entity_id');
      $result = $query
        ->fields('s', ['entity_id'])
        ->condition('d.field_day_value', $now_day_of_week)
        ->condition('t.field_time_from', $seconds_since_midnight, '<=')
        ->condition('t.field_time_to', $seconds_since_midnight, '>=')
        ->execute()
        ->fetchAllKeyed(0,0);
    // Add to the result all nodes without schedules set
      // We treat these services as always open
      // first get all services with schedules set
      $subquery = $this->database->select('node_field_data', 'n');
      $subquery->join('node__field_schedule', 's', 'n.nid = s.entity_id');
      $sub_result = $subquery
      ->fields('n' ['nid'])
      ->condition('n.status', 1)
      ->condition('n.type', 'service')->execute()->fetchAllKeyed(0,0);
      $query = $this->database->select('node_field_data', 'n');
      // then we get all services that do not have schedules set
      $result2 = $query
        ->fields('n' ['nid'])
        ->condition('n.status', 1)
        ->condition('n.type', 'service')
        ->condition('n.nid', $sub_result, 'NOT IN')
        ->execute()
        ->fetchAllKeyed(0,0);
    // Add where clause to view query.
      $this->query->addWhere('AND', 'node_field_data.nid', array_merge($result2, $result), 'IN');
  }

  protected function valueForm(&$form, FormStateInterface $form_state) {
    if ($exposed = $form_state->get('exposed')) {
      $filter_form_type = 'checkbox';
    }
    else {
      $filter_form_type = 'radios';
    }
    $form['value'] = [
      '#type' => $filter_form_type,
      '#title' => $this->t('Open Now'),
      '#options' => [0 => $this->t('Open Now'), 1 => $this->t('Closed')],
      '#default_value' => 0,
    ];
  }
}
