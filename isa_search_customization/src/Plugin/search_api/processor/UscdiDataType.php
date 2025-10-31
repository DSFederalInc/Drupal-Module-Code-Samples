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
 *   id = "uscdi_data_type",
 *   label = @Translation("USCDI Data Type"),
 *   description = @Translation("Add a field to indicate whether it is a USCDI Data Class or Element"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class UscdiDataType extends ProcessorPluginBase {
  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('USCDI Data Type'),
        'description' => $this->t('USCDI Data Type'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['uscdi_data_type'] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasource_id = $item->getDatasourceId();
    $uscdi_data_type = '';
    if($datasource_id == 'entity:taxonomy_term') {
      $taxonomy_term = $item->getOriginalObject()->getValue();
      $taxonomy_vocabulary = $taxonomy_term->bundle();
      //$tid = $taxonomy_term->id();
      if($taxonomy_vocabulary == 'uscdi_data_class') {
        $uscdi_data_type = 'Data Class';
      }
      elseif($taxonomy_vocabulary == 'uscdi_data_class_elements') {
        $uscdi_data_type = 'Data Element';
      }
    }
    elseif($datasource_id == 'entity:paragraph') {
      $paragraph = $item->getOriginalObject()->getValue();
      if($paragraph->getType() == 'uscdi_version') {
        //kint($paragraph->get('field_data_element_name'));
        $uscdi_data_type = 'Data Element';
      }
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'uscdi_data_type');
    foreach($fields as $field) {
      $field->addValue($uscdi_data_type);
    }
  }
}
