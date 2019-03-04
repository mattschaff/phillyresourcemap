<?php

namespace Drupal\time_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\time_field\Time;

/**
 * Plugin implementation of the 'time_widget' widget.
 *
 * @FieldWidget(
 *   id = "time_range_widget",
 *   label = @Translation("Time range widget"),
 *   field_types = {
 *     "time_range"
 *   }
 * )
 */
class TimeRangeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['from'] = [
      '#title' => $this->t('Start time'),
      '#type' => 'time',
    ];
    $element['to'] = [
      '#title' => $this->t('End time'),
      '#type' => 'time',
    ];
    if ($items[$delta]->from) {
      $element['from']['#default_value'] = isset($items[$delta]->from) ? Time::createFromTimestamp($items[$delta]->from)
        ->formatForWidget() : NULL;
    }

    if ($items[$delta]->to) {
      $element['to']['#default_value'] = isset($items[$delta]->to) ? Time::createFromTimestamp($items[$delta]->to)
        ->formatForWidget() : NULL;
    }

    return $element;
  }

}
