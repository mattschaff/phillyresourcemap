<?php

/**
 * @file
 * Definition of Drupal\link_core\Plugin\views\field\OpenWhen
 */

namespace Drupal\link_core\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Returns the next time a resource map service is open
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("open_when")
 */
class OpenWhen extends FieldPluginBase {

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
    $node = $values->_entity;
    return $this->t('Meow');
  }
}
