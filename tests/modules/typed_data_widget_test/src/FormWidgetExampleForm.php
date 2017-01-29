<?php

namespace Drupal\typed_data_widget_test;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\typed_data\Form\SubformState;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\typed_data\Widget\FormWidgetManagerTrait;

/**
 * Class FormWidgetExampleForm.
 */
class FormWidgetExampleForm extends FormBase {

  use FormWidgetManagerTrait;
  use TypedDataTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'typed_data_widget_test_form';
  }

  /**
   * @param $widget_id
   *
   * @return \Drupal\Core\Plugin\Context\ContextDefinitionInterface
   */
  public function getExampleContextDefinition($widget_id) {
    switch ($widget_id) {
      default:
      case 'text_input':
        return ContextDefinition::create('string')
          ->setLabel('Example string')
          ->setDescription('Some example string')
          ->setDefaultValue('default');
      case 'select':
        return ContextDefinition::create('filter_format')
          ->setLabel('Filter format')
          ->setDescription('Some example selection.');
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $widget_id = NULL) {
    $widget = $this->getFormWidgetManager()->createInstance($widget_id);

    // Read and write widget configuration into the state.
    // @todo: Create a StateTrait in core and use it here.
    $state = \Drupal::state();
    // Allow tests to define a custom context definition.
    $context_definition = $state->get('typed_data_widgets.definition');
    $context_definition = $context_definition ?: $this->getExampleContextDefinition($widget_id);
    $form_state->set('widget_id', $widget_id);
    $form_state->set('context_definition', $context_definition);

    // Create a typed data object.
    $data = $this->getTypedDataManager()
      ->create($context_definition->getDataDefinition());
    $value = $state->get('typed_data_widgets.' . $widget_id);
    $value = isset($value) ? $value : $context_definition->getDefaultValue();
    $data->setValue($value);

    $subform_state = SubformState::createWithParents(['data'], $form, $form_state);
    $form['data'] = $widget->form($data, $subform_state);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $context_definition = $form_state->get('context_definition');
    $widget_id = $form_state->get('widget_id');
    $widget = $this->getFormWidgetManager()->createInstance($widget_id);

    $subform_state = SubformState::createWithParents(['data'], $form, $form_state);
    $data = $this->getTypedDataManager()
      ->create($context_definition->getDataDefinition());
    $widget->extractFormValues($data, $subform_state);

    // Validate the data and flag possible violations.
    $violations = $data->validate();
    $widget->flagErrors($data, $violations, $subform_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $context_definition = $form_state->get('context_definition');
    $widget_id = $form_state->get('widget_id');
    $widget = $this->getFormWidgetManager()->createInstance($widget_id);

    $subform_state = SubformState::createWithParents(['data'], $form, $form_state);
    $data = $this->getTypedDataManager()
      ->create($context_definition->getDataDefinition());
    $widget->extractFormValues($data, $subform_state);

    // Read and write widget configuration into the state.
    // @todo: Create a StateTrait in core and use it here.
    $state = \Drupal::state();
    $state->set('typed_data_widgets.' . $widget_id, $data->getValue());
    drupal_set_message('Value saved');
  }
}