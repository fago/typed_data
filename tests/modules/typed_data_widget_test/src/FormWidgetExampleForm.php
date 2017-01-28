<?php

namespace Drupal\typed_data_widget_test;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\typed_data\Widget\FormWidgetManagerTrait;

/**
 * Class FormWidgetExampleForm.
 */
class FormWidgetExampleForm extends FormBase {

  use FormWidgetManagerTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'typed_data_widget_test_form';
  }

  public function getExampleContextDefinition($widget_id) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $widget_id = NULL) {


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

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}