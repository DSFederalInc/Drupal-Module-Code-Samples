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
 *   id = "uscdi_data_element_name",
 *   label = @Translation("USCDI Data Element Name"),
 *   description = @Translation("Add a field for Data Element Name"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class UscdiDataElementName extends ProcessorPluginBase {
    /**
     * {@inheritdoc}
     */
    public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
        $properties = [];

        if (!$datasource) {
            $definition = [
                'label' => $this->t('USCDI Data Element Name'),
                'description' => $this->t('USCDI Data Element Name'),
                'type' => 'string',
                'processor_id' => $this->getPluginId(),
            ];
            $properties['uscdi_data_element_name'] = new ProcessorProperty($definition);
        }
        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldValues(ItemInterface $item) {
        $datasource_id = $item->getDatasourceId();
        $uscdi_data_element_name = '';
        if($datasource_id == 'entity:paragraph') {
            $paragraph = $item->getOriginalObject()->getValue();
            if($paragraph->getType() == 'uscdi_version'){
                $paragraph_data_element_name = $paragraph->get('field_data_element_name')->getString();
                if($paragraph_data_element_name) {
                    $uscdi_data_element_name = $paragraph_data_element_name;
                }
                else {
                    $uscdi_data_element_name = $paragraph->getParentEntity()->getName();
                }
            }
        }

        $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'uscdi_data_element_name');
        foreach($fields as $field) {
            $field->addValue($uscdi_data_element_name);
        }
    }
}
