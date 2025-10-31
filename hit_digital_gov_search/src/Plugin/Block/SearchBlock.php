<?php
/**
 * @file
 * Contains \Drupal\hit_digital_gov_search\src\Plugin\Block\SearchBlock.
 */

namespace Drupal\hit_digital_gov_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a 'SearchBlock' block.
 *
 * @Block(
 *  id = "hit_digitalgov_block",
 *  admin_label = @Translation("Search"),
 * )
 */
class SearchBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\hit_digital_gov_search\Form\SearchForm');
    return $form;
  }
}
