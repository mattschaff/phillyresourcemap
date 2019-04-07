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
    //$options['node_type'] = array('default' => 'service');

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
    $list = $this->openTimesService->getList();
    $node = $values->_entity;
    return $this->t($list[0]);
  }
}
