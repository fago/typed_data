<?php

namespace Drupal\Tests\typed_data\Functional\TypedDataFormWidget;

use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\ListDataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\Core\TypedData\TypedDataTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\typed_data\Traits\BrowserTestHelpersTrait;
use Drupal\typed_data\Util\StateTrait;
use Drupal\typed_data\Widget\FormWidgetManagerTrait;

/**
 * Class TextInputWidgetTest.
 *
 * @group typed_data
 *
 * @coversDefaultClass \Drupal\typed_data\Plugin\TypedDataFormWidget\TextareaWidget
 */
class TextareaWidgetTest extends BrowserTestBase {

  use BrowserTestHelpersTrait;
  use FormWidgetManagerTrait;
  use StateTrait;
  use TypedDataTrait;

  /**
   * The tested form widget.
   *
   * @var \Drupal\typed_data\Widget\FormWidgetInterface
   */
  protected $widget;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'typed_data',
    'typed_data_widget_test',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->widget = $this->getFormWidgetManager()->createInstance('textarea');
  }

  /**
   * @covers ::isApplicable
   */
  public function testIsApplicable() {
    $this->assertTrue($this->widget->isApplicable(DataDefinition::create('string')));
    $this->assertFalse($this->widget->isApplicable(ListDataDefinition::create('string')));
    $this->assertFalse($this->widget->isApplicable(MapDataDefinition::create()));
  }

  /**
   * @covers ::form
   * @covers ::extractFormValues
   */
  public function testFormEditing() {
    $context_definition = ContextDefinition::create('string')
      ->setLabel('Example string')
      ->setDescription('Some example string')
      ->setDefaultValue('default1');
    $this->getState()->set('typed_data_widgets.definition', $context_definition);

    $this->drupalLogin($this->createUser([], NULL, TRUE));
    $path = 'admin/config/user-interface/typed-data-widgets/' . $this->widget->getPluginId();
    $this->drupalGet($path);

    $this->assertSession()->elementTextContains('css', 'label[for=edit-data-value]', $context_definition->getLabel());
    $this->assertSession()->elementTextContains('css', 'div[id=edit-data-value--description]', $context_definition->getDescription());
    $this->assertSession()->fieldValueEquals('data[value]', $context_definition->getDefaultValue());

    $this->fillField('data[value]', 'jump');
    $this->pressButton('Submit');

    $this->drupalGet($path);
    $this->assertSession()->fieldValueEquals('data[value]', 'jump');
  }

}
