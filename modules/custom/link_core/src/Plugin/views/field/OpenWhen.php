<?php

/**
 * @file
 * Definition of Drupal\link_core\Plugin\views\field\OpenWhen
 */

namespace Drupal\link_core\Plugin\views\field;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\link_core\Service\OpenTimes;
use Drupal\node\Entity\Node;

/**
 * Returns the next time a resource map service is open
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("open_when")
 */
class OpenWhen extends FieldPluginBase {

  /**
   * OpenTimes service
   *
   * @var \Drupal\link_core\Service\OpenTimes
   */
  private $openTimesService;

  /**
   * Constructor
   *
   * @param array $configuration
   * @param [type] $plugin_id
   * @param [type] $plugin_definition
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, OpenTimes $openTimesService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->openTimesService = $openTimesService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('link_core.open_times')
    );
  }

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    // Get service node.
    $service_node = $values->_entity;
    // Get the next time that service is open.
    $open_time_array = $this->getNextOpenTime($service_node);
    // Render output.
    $output = '<div class="open-' . $open_time_array['class'] .'">' . $open_time_array['text'] . '</div>';
    return [
      '#type' => 'inline_template',
      '#template' => $output,
      ];
  }

  /**
   * Gets the next time a service is open
   *
   * @param Drupal\node\Entity\Node $node
   * @return array $open_time_array
   */
  protected function getNextOpenTime(Node $node){
    // Get open times list.
    $list = $this->openTimesService->getList();
    $class = 'now';
    $text = $this->t('Open Now');
    if (isset($list[$node->id()])) {
      $class = 'sometime';
      $text = $this->t('Opens Thurs 12 p.m.');
      // Get the current day and time.
      // Day pointer = current day
      // Loop through 0 to 6
        // Loop through service days
          // If current day == service day
            // If open time < current time && close time > curren time
              // Return open now.
            // Else: iterator to next # loop.
          // Else if day pointer == service day
            // Next time = formatted open time for service day
              // next_service_day = day pointer - current day
                // if positive -> strtime("today +" . strval(next_service_day))
                // else -> strtime("today +" . strval(7 + next_service_day)
        // If Day pointer < 6
            // Day pointer++
        // Else: Day pointer = 0

    }
    return [
      'class' => $class,
      'text' => $text,
    ];
  }
}
