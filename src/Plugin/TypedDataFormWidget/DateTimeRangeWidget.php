<?php

namespace Drupal\typed_data\Plugin\TypedDataFormWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\Type\DateTimeInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\typed_data\Form\SubformState;
use Drupal\typed_data\Widget\FormWidgetBase;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @TypedDataFormWidget(
 *   id = "datetime_range",
 *   label = @Translation("DateTime Range"),
 *   description = @Translation("A datetime range input widget."),
 * )
 */
class DateTimeRangeWidget extends FormWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'label' => NULL,
      'description' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(DataDefinitionInterface $definition) {
    return is_subclass_of($definition->getClass(), DateTimeInterface::class);
  }

  /**
   * {@inheritdoc}
   */
  public function form(TypedDataInterface $data, SubformStateInterface $form_state) {
    $value = $data->getValue();

    $form = SubformState::getNewSubForm();
    $form['#theme_wrappers'][] = 'fieldset';

    $form['#title'] = $this->configuration['label'] ?: $data->getDataDefinition()->getLabel();
    $form['#description'] = $this->configuration['description'] ?: $data->getDataDefinition()->getDescription();

    $form['#element_validate'][] = [$this, 'validateStartEnd'];

    $form['value'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start date'),
      '#default_value' => (isset($value['value'])) ? new DrupalDateTime($value['value']) : '',
    ];
    $form['end_value'] = [
      '#type' => 'datetime',
      '#title' => $this->t('End date'),
      '#default_value' => (isset($value['end_value'])) ? new DrupalDateTime($value['end_value']) : '',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function extractFormValues(TypedDataInterface $data, SubformStateInterface $form_state) {
    $value = $form_state->getValue('value');
    $end_value = $form_state->getValue('end_value');
    if ($value instanceof DrupalDateTime) {
      $value = $value->format('Y-m-d H:i:s');
    }
    if ($end_value instanceof DrupalDateTime) {
      $end_value = $end_value->format('Y-m-d H:i:s');
    }
    $data->setValue([
      'value' => $value,
      'end_value' => $end_value,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function flagViolations(TypedDataInterface $data, ConstraintViolationListInterface $violations, SubformStateInterface $formState) {
    foreach ($violations as $violation) {
      /** @var ConstraintViolationInterface $violation */
      $formState->setErrorByName('value', $violation->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationDefinitions(DataDefinitionInterface $definition) {
    return [
      'label' => DataDefinition::create('string')
        ->setLabel($this->t('Label')),
      'description' => DataDefinition::create('string')
        ->setLabel($this->t('Description')),
    ];
  }

  /**
   * #element_validate callback to ensure that the start date <= the end date.
   *
   * @param array $element
   *   An associative array containing the properties and children of the
   *   generic form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public function validateStartEnd(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $start_date = $element['value']['#value']['object'];
    $end_date = $element['end_value']['#value']['object'];

    if ($start_date instanceof DrupalDateTime && $end_date instanceof DrupalDateTime) {
      if ($start_date->getTimestamp() !== $end_date->getTimestamp()) {
        $interval = $start_date->diff($end_date);
        if ($interval->invert === 1) {
          $form_state->setError($element, $this->t('The @title end date cannot be before the start date', ['@title' => $element['#title']]));
        }
      }
    }
  }

}
