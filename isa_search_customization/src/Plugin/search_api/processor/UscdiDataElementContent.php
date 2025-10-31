<?php

namespace Drupal\isa_search_customization\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\Core\Database\Database;

/**
 * Adds a content area field to each indexed data.
 *
 * @SearchApiProcessor(
 *   id = "uscdi_data_element_content",
 *   label = @Translation("USCDI Data Element Content"),
 *   description = @Translation("Extra content on data element page"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class UscdiDataElementContent extends ProcessorPluginBase {
  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('USCDI Data Element content'),
        'description' => $this->t('USCDI Data Element content'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['uscdi_data_element_content'] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $datasource_id = $item->getDatasourceId();
    $uscdi_data_element_content = '';

    if($datasource_id == 'entity:paragraph') {
      $paragraph = $item->getOriginalObject()->getValue();
      if($paragraph->getType() == 'uscdi_version') {
        $pid = $paragraph->id();
        if ($paragraph->field_classification_level->entity != null) {
          $view_mode = $paragraph->field_classification_level->entity->getName();
        } else {
          $view_mode = '';
        }
        $view_mode = str_replace(' ', '-', strtolower($view_mode));
        $data_element_entity = $paragraph->getParentEntity();

        $view_builder = \Drupal::entityTypeManager()->getViewBuilder('taxonomy_term');
        $output = $view_builder->view($data_element_entity, $view_mode);
        $html = \Drupal::service('renderer')->renderPlain($output);
        $txt = htmlspecialchars(trim(strip_tags($html)));
        $uscdi_data_element_content = preg_replace('/\s+/', ' ', $txt);

        $connection = Database::getConnection();
        $uscdi_submission_nid = $connection->query('SELECT entity_id FROM {node__field_uscdi_data_element}
INNER JOIN {paragraphs_item_field_data}
ON node__field_uscdi_data_element.field_uscdi_data_element_target_id =  paragraphs_item_field_data.parent_id
WHERE  paragraphs_item_field_data.id = :pid', array(':pid' => $pid))->fetchField();

        if (!empty($uscdi_submission_nid)) {
          $uscdi_submission_node = \Drupal::entityTypeManager()->getStorage('node')->load($uscdi_submission_nid);
          $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
          $output_1 = $view_builder->view($uscdi_submission_node, 'uscdi_data_element_page_display');
          $html_1 = \Drupal::service('renderer')->renderPlain($output_1);
          $txt_1 = htmlspecialchars(trim(strip_tags($html_1)));
          $uscdi_data_element_content .= preg_replace('/\s+/', ' ', $txt_1);
        }

      }
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'uscdi_data_element_content');
    foreach($fields as $field) {
      $field->addValue($uscdi_data_element_content);
    }
  }
}

