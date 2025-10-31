<?php

namespace Drupal\isa_search_customization\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\Component\Utility\Html;

/**
 * Adds a content area field to each indexed data.
 *
 * @SearchApiProcessor(
 *   id = "extra_content_field",
 *   label = @Translation("Extra Content Field"),
 *   description = @Translation("Add any extra content that is on the page"),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class AddExtraContent extends ProcessorPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL)
  {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Extra Content Field'),
        'description' => $this->t('Any extra content on the page'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['extra_content_field'] = new ProcessorProperty($definition);
    }
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item)
  {
    $extra_content = '';

    if($datasource_id == 'entity:node') {
      $node = $item->getOriginalObject()->getValue();
      $nid = $node->id();

      if(!empty($nid) && in_array($nid, [1421, 2281])) {
        $extra_content = $this->scrapeContent($host_name.'/isp/node/'.$nid, 'node');
      }
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($item->getFields(), NULL, 'extra_content_field');
    foreach ($fields as $field) {
      $field->addValue($extra_content);
    }
  }

  private function scrapeContent($url, $type) {
    $curl = curl_init($url);
    $host_name = \Drupal::request()->getSchemeAndHttpHost();
    $config = \Drupal::config('isa_search_customization.settings');
    $verify_ssl = $config->get('verify_ssl') ?? TRUE;

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $verify_ssl);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $verify_ssl ? 2 : 0);

    // For development environments only, you can disable via configuration
    // Never disable in production
    
    $config = \Drupal::config('isa_search_customization.settings');
    $basic_auth = $config->get('basic_auth_credentials');

    if (!empty($basic_auth)) {
      $headers = array(
        'Authorization: Basic ' . $basic_auth,
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    $page = curl_exec($curl);
    if (curl_errno($curl)) // check for execution errors
    {
      echo "Scraper error: " . curl_error($curl);
      exit;
    }
    curl_close($curl);

    $block_text = "";
    $DOM = new \DOMDocument;
    libxml_use_internal_errors(true);
    if (!$DOM->loadHTML($page)) {
      $errors = "";
      foreach (libxml_get_errors() as $error) {
        $errors.= $error->message . "<br/>";
      }
      libxml_clear_errors();
      print "libxml errors:<br>$errors";
      return;
    }

    $xpath = new \DOMXPath($DOM);
    $query_arg = [];
    if($type == 'node') {
      $query_arg = [
        '//div[@id="block-uscdicalloutbox"]/div/p/text()',
        '//div[@id="uscdi-data-class-tabs"]/div//text()',
        '//div[@id="blocktabs-isa_toc_new"]/div//text()',
      ];
    }
//    elseif($type == 'uscdi_data_element') {
//      $query_arg = [
//        "//table[contains(concat(' ',normalize-space(@class),' '),' data-element-page ')]//text()",
//        //"//section[contains(concat(' ',normalize-space(@id),' '),'comment-')]//text()",
//      ];
//    }

    foreach($query_arg as $query) {
      $entries = $xpath->query($query);
      foreach ($entries as $entry) {
        $block_text .= $entry->data;
      }
    }

    $block_text = preg_replace('/\s\s+/', ' ', $block_text);
/*    if($block_text === '') {
      $block_text = "some prepoluated content";
    }*/

    return $block_text;
  }
}
