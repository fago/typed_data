<?php

namespace Drupal\typed_data\Plugin\TypedDataFilter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\TypedData\EntityDataDefinitionInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\typed_data\DataFilterBase;

/**
 * A data filter that provides the URL of an entity.
 *
 * @DataFilter(
 *   id = "entity_url",
 *   label = @Translation("Provides the URL of an entity."),
 * )
 */
class EntityUrlFilter extends DataFilterBase {

  /**
   * {@inheritdoc}
   */
  public function filter(DataDefinitionInterface $definition, $value, array $arguments, BubbleableMetadata $bubbleable_metadata = NULL) {
    assert($value instanceof EntityInterface);
    // @todo: url() is deprecated, but toUrl() does not work for file entities,
    // thus move to url() once toUrl() works for file entities also.
    return $value->url();
  }

  /**
   * {@inheritdoc}
   */
  public function canFilter(DataDefinitionInterface $definition) {
    return $definition instanceof EntityDataDefinitionInterface;
  }

  /**
   * {@inheritdoc}
   */
  public function filtersTo(DataDefinitionInterface $definition, array $arguments) {
    return 'uri';
  }

}
