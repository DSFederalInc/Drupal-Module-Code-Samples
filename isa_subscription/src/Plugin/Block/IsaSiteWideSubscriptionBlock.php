<?php
/**
 * @file
 * Contains \Drupal\isa_subscription\Plugin\Block\IsaSiteWideSubscriptionBlock.
 */

namespace Drupal\isa_subscription\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
/**
 * Provides a 'isa_site_wide_subscription' block.
 *
 * @Block(
 *   id = "isa_site_wide_subscription",
 *   admin_label = @Translation("ISA Site Wide Subscription block"),
 *   category = @Translation("Custom ISA Site Wide Subscription block")
 * )
 */

class IsaSiteWideSubscriptionBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

  	//REVISION ISSUE FIX
  	$current_route=\Drupal::routeMatch()->getRouteName();
                //Do not load on revision page 
	 if(!strstr($current_route,"revision"))
	        {
	   $form = \Drupal::formBuilder()->getForm('Drupal\isa_subscription\Form\IsaSiteWideSubscriptionForm');
	   return $form;
	 }



   /* $form = \Drupal::formBuilder()->getForm('Drupal\isa_subscription\Form\IsaSiteWideSubscriptionForm');
    return $form;*/
   }
}



