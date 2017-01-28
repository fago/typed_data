<?php

namespace Drupal\typed_data\Plugin\TypedDataFormWidget;

use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\OptionsProviderInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\typed_data\Widget\ContextDefinitionInterface;
use Drupal\typed_data\Widget\FormWidgetBase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @TypedDataFormWidget(
 *   id = "select",
 *   label = @Translation("Select"),
 *   description = @Translation("A simple select box."),
 * )
 */
class SelectWidget extends FormWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'label' => NULL,
      'description' => NULL,
      'empty_option' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(DataDefinitionInterface $definition) {
    return is_subclass_of($definition->getClass(), OptionsProviderInterface::class);
  }

  /**
   * {@inheritdoc}
   */
  public function form(TypedDataInterface $data, SubformStateInterface $form_state) {
    return [
      '#type' => 'select',
      '#title' => $this->configuration['label'] ?: $data->getDataDefinition()->getLabel(),
      '#description' => $this->configuration['description'] ?: $data->getDataDefinition()->getDescription(),
      '#default_value' => $data->getValue(),
      '#multiple' => $this->configuration['multiple'],
      '#empty_option' => $this->configuration['empty_option'],
      '#empty_value' => '',
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
      'label' => ContextDefinition::create('string')
        ->setLabel($this->t('Label')),
      'description' => ContextDefinition::create('string')
        ->setLabel($this->t('Description')),
      'empty_option' => ContextDefinition::create('string')
        ->setLabel($this->t('Empty option label'))
        ->setDescription($this->t('Allows overriding the label of the empty option')),
    ];
  }

}
