<?php

namespace Drupal\typed_data\Plugin\TypedDataFormWidget;

use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\Type\StringInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\typed_data\Widget\ContextDefinitionInterface;
use Drupal\typed_data\Widget\FormWidgetBase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @TypedDataFormWidget(
 *   id = "text_input",
 *   label = @Translation("Text input"),
 *   description = @Translation("A simple, one-line text input widget."),
 * )
 */
class TextInputWidget extends FormWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'label' => NULL,
      'description' => NULL,
      'placeholder' => NULL,
      'size' => 60,
      'maxlength' => 255,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(DataDefinitionInterface $definition) {
    return is_subclass_of($definition->getClass(), StringInterface::class);
  }

  /**
   * {@inheritdoc}
   */
  public function form(TypedDataInterface $data, SubformStateInterface $form_state) {
    return [
      '#type' => 'text',
      '#title' => $this->configuration['label'] ?: $data->getDataDefinition()->getLabel(),
      '#description' => $this->configuration['description'] ?: $data->getDataDefinition()->getDescription(),
      '#default_value' => $data->getValue(),
      '#placeholder' => $this->configuration['placeholder'],
      '#size' => $this->configuration['size'],
      '#maxlength' => $this->configuration['maxlength'],
      '#required' => $data->getDataDefinition()->isRequired(),
      '#disabled' => $data->getDataDefinition()->isReadOnly(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function extractFormValues(TypedDataInterface $data, array $element, SubformStateInterface $form_state) {
    // Ensure empty values correctly end up as NULL value.
    if ($element['#value'] === '') {
      $element['#value'] = NULL;
    }
    $data->setValue($element['#value']);
  }

  /**
   * {@inheritdoc}
   */
  public function flagErrors(TypedDataInterface $data, ConstraintViolationListInterface $violations, array $element, SubformStateInterface $form_state) {
    foreach ($violations as $offset => $violation) {
      /** @var ConstraintViolationInterface $violation */
      $form_state->setError($element, $violation->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationDataDefinitions(DataDefinitionInterface $definition) {
    return [
      'label' => DataDefinition::create('string')
        ->setLabel($this->t('Label')),
      'description' => DataDefinition::create('string')
        ->setLabel($this->t('Description')),
      'placeholder' => DataDefinition::create('string')
        ->setLabel($this->t('Placeholder value')),
      'size' => DataDefinition::create('integer')
        ->setLabel($this->t('Input field size')),
      'maxlength' => DataDefinition::create('integer')
        ->setLabel($this->t('Maximum text length')),
    ];
  }

}
