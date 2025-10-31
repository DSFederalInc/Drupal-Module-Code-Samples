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
 *   id = "uscdi_classification_level",
 *   label = @Translation("USCDI Classification Level"),
 *   description = @Translation("Add a field to indicate the classification level of a USCDI Data Class or Element"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class UscdiClassificationLevel extends ProcessorPluginBase {
  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('USCDI Classification Level'),
        'description' => $this->t('USCDI Classification Level'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['uscdi_classification_level'] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasource_id = $item->getDatasourceId();
    $uscdi_classification_level = '';
/*    if($datasource_id == 'entity:taxonomy_term') {
      $taxonomy_term = $item->getOriginalObject()->getValue();
      $taxonomy_vocabulary = $taxonomy_term->bundle();
      if($taxonomy_vocabulary == 'uscdi_data_class') {
        //TODO: make improvements later on to include classification level for data class
      }
    }*/

    if($datasource_id == 'entity:paragraph') {
      $paragraph = $item->getOriginalObject()->getValue();
      if($paragraph->getType() == 'uscdi_version') {
        $paragraph_classification_level = $paragraph->get('field_classification_level');
        if ($paragraph_classification_level != null) {
          if ($paragraph_classification_level->first() != null) {
            if ($paragraph_classification_level->first()->get('entity') != null) {
              if ($paragraph_classification_level->first()->get('entity')->getTarget() != null) {
                if(isset($paragraph_classification_level) && $paragraph_classification_level->first()->get('entity')->getTarget()->getValue()->getName() != 'Draft USCDI V2') {
                  $uscdi_classification_level = $paragraph_classification_level->first()->get('entity')->getTarget()->getValue()->getName();
                }
              } else {
                $uscdi_classification_level = ''; // todo - we might need to see which field we have deleted
              }
             } else {
              $uscdi_classification_level = 'Test String 2';
            }
          } else {
            $uscdi_classification_level = 'Test String 3';
          }
          
        } else {
          $uscdi_classification_level = 'Test String 4';
        }
      }
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'uscdi_classification_level');
    foreach($fields as $field) {
      $field->addValue($uscdi_classification_level);
    }
  }
}
