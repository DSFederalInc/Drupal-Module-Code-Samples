<?php

namespace Drupal\isa_search_customization\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds a content area field to each indexed data.
 *
 * @SearchApiProcessor(
 *   id = "isa_content_area",
 *   label = @Translation("ISA content area"),
 *   description = @Translation("Add an ISA content area field"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class IsaContentArea extends ProcessorPluginBase {
  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('ISA Content Area'),
        'description' => $this->t('ISA content area'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['isa_content_area'] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasource_id = $item->getDatasourceId();
    $content_area = '';
    if($datasource_id == 'entity:node') {
      $node = $item->getOriginalObject()->getValue();
      $nid = $node->id();
      $content_type = $node->getType();
      if($content_type == 'standard' or $nid== '2106') {
        $content_area = 'SVAP';
      }
      elseif($nid == '2281' or $nid == '2291' or $nid == '2386') {
        $content_area = 'USCDI';
      }
      elseif($content_type == 'isa_document' or $content_type == 'page') {
        $content_area = 'ISA';
      }
    }
    elseif($datasource_id == 'entity:taxonomy_term') {
      $taxonomy_term = $item->getOriginalObject()->getValue();
      $taxonomy_vocabulary = $taxonomy_term->bundle();
      if($taxonomy_vocabulary == 'uscdi_data_class_elements' or $taxonomy_vocabulary == 'uscdi_data_class') {
        $content_area = 'USCDI';
      }
      elseif($taxonomy_vocabulary == 'tags') {
        $content_area = 'ISA';
      }
      elseif($taxonomy_vocabulary == 'certification_criteria') {
        $content_area = 'SVAP';
      }
    }
    elseif($datasource_id == 'entity:paragraph') {
      $paragraph = $item->getOriginalObject()->getValue();
      if($paragraph->getType() == 'uscdi_version') {
        $content_area = 'USCDI';
      }
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'isa_content_area');
    foreach($fields as $field) {
      $field->addValue($content_area);
    }
  }
}
