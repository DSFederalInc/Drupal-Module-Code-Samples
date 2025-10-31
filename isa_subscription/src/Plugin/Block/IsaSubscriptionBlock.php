<?php
/**
 * @file
 * Contains \Drupal\isa_subscription\Plugin\Block\IsaSubscriptionBlock.
 */

namespace Drupal\isa_subscription\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
/**
 * Provides a 'isa_subscription' block.
 *
 * @Block(
 *   id = "isa_subscription",
 *   admin_label = @Translation("ISA Subscription block"),
 *   category = @Translation("Custom ISA Subscription block")
 * )
 */

class IsaSubscriptionBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

//REVISION ISSUE FIX
  	$current_route=\Drupal::routeMatch()->getRouteName();
                //Do not load on revision page 
	 if(!strstr($current_route,"revision"))
	        {
	   $form = \Drupal::formBuilder()->getForm('Drupal\isa_subscription\Form\IsaSubscriptionForm');
	   return $form;
	 }
  	
    /*$form = \Drupal::formBuilder()->getForm('Drupal\isa_subscription\Form\IsaSubscriptionForm');
    return $form;*/
   }
}