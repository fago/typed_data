<?php

namespace Drupal\typed_data\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SubformState.
 */
class SubformState extends \Drupal\Core\Form\SubformState {

  /**
   * The array parents; i.e., where the sub-form is located in the parent.
   *
   * @var string[]
   */
  protected $arrayParents;

  /**
   * Creates a new subform by specifying the array parents.
   *
   * @param string[] $arrayParents
   *   The array parents; i.e., where the sub-form is located in the parent.
   *   For example if a sub-form is located in $form['fieldset']['parent'], the
   *   array parents would be "fieldset" and "parent".
   * @param mixed[] $parentForm
   *   The subform's complete parent form array.
   * @param \Drupal\Core\Form\FormStateInterface $parentFormState
   *   The parent form state.
   *
   * @return static
   */
  public static function createWithParents($arrayParents, array &$parentForm, FormStateInterface$parentFormState) {
    $exists = NULL;
    $form = &NestedArray::getValue($parentForm, $arrayParents, $exists);
    if (!$exists) {
      $form = static::getNewSubForm();
    }
    $instance = parent::createForSubform($form, $parentForm, $parentFormState);
    $instance->arrayParents = $arrayParents;
    return $instance;
  }

  /**
   * Gets a new sub-form array.
   *
   * Sub-forms must have #tree set to TRUE, so this is set as default.
   *
   * @return mixed[]
   *   The new sub-form.
   */
  public static function getNewSubForm() {
    return [
      '#tree' => TRUE,
    ];
  }

}