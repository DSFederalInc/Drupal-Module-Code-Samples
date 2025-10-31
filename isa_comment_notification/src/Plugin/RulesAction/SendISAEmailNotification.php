<?php

/**
 * @file
 * Contains \Drupal\isa_comment_notification\Plugin\RulesAction\SendISAEmailNotification.
 */

namespace Drupal\isa_comment_notification\Plugin\RulesAction;

//use Drupal\custom_pub\CustomPublishingOptionInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Component\Utility\Html;
/**
 * Provides an action to trigger a custom publishing option.
 *
 * @RulesAction(
 *   id = "rules_sent_isa_email_notification",
 *   label = @Translation("Add Custom Email Notification on Comment Submission"),
 *   category = @Translation("Comment"),
 *   context_definitions = {
 *    "entity_id" = @ContextDefinition("integer",
 *       label = @Translation("Entity ID"),
 *       description = @Translation("Specifies the entity ID, which the comment comes from.")
 *     )
 *   }
 * )
 */
class SendISAEmailNotification extends RulesActionBase {

  /**
   * Send custom email 
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be saved.
   * 
   *   
   */
  protected function doExecute($entity_id = NULL) {


    if (isset($entity_id))
    {


     $EntityId=$entity_id;

     $OwnerEmail= $this->getWorkbeckAccessUser($EntityId);
                  $this->sendCommentEmail($EntityId,$OwnerEmail);
     
}
return 1;


  }

 // Get user assigned to the section 
    public function  getWorkbeckAccessUser($node_id)
  {
    $node = \Drupal\node\Entity\Node::load($node_id); // load the host node

    $SectionTaxonomy=$node->field_section_taxonomy->target_id;;
    

        $WorkBechValue = \Drupal::database()->query('SELECT entity_id FROM {user__field_workbench_access} WHERE field_workbench_access_value = :wid', array(':wid' => $SectionTaxonomy))->execute();
        //Get Parent Taxonomy Term 
        $TaxonomyParentId=$this->getTaxonomyParentID($SectionTaxonomy);
        $WorkBechValue = \Drupal::database()->query('SELECT entity_id FROM {user__field_workbench_access} WHERE field_workbench_access_value = :wid or field_workbench_access_value = :pid'  , array(':wid' => $SectionTaxonomy,':pid' => $TaxonomyParentId))->execute();

        
        $OwnerEmailArray=[];
        $WorkBechValueRecords = $WorkBechValue->fetchAll();
       


                        if(count($WorkBechValueRecords)>0)
                          {
                            
                         foreach ($WorkBechValueRecords as $WorkBechValueRecord) {
                            // Get Users Email
                               $UserId=$WorkBechValueRecord->entity_id;
                               $AssignedUerEmail=$this->getIsaUser($UserId);
                               if($AssignedUerEmail!="")
                               {                               
                               array_push($OwnerEmailArray, $AssignedUerEmail);
                               }
                             
                             } 
                              
                        
                        }
                        else
                        {

                          $DefaultUser=1;
                        }

          

            if(count($OwnerEmailArray)>1)
            {
                   $OwnerEmails=implode(',', $OwnerEmailArray);

              return $OwnerEmails;

            }
            elseif (count($OwnerEmailArray)==1) {
                return $OwnerEmailArray['0'];
            }
            elseif ($DefaultUser==1) {
                $config = \Drupal::config('isa_comment_notification.settings');
                $default_email = $config->get('default_notification_email');
                return $default_email ?: \Drupal::config('system.site')->get('mail');
            }
            else {
                $config = \Drupal::config('isa_comment_notification.settings');
                $default_email = $config->get('default_notification_email');
                return $default_email ?: \Drupal::config('system.site')->get('mail');
}

     
  } // this will return  user assigned to the section 


//Than using Entity Load just get Users Email address by direct query 
   public function getIsaUser($user_id)
   {
              $IsaUserQuery = \Drupal::database()->query('SELECT mail FROM {users_field_data} WHERE uid = :uid', array(':uid' => $user_id))->execute();

              $IsaUsers= $IsaUserQuery->fetchAll();
               
               foreach ($IsaUsers as $IsaUser) {
                 return $IsaUser->mail;
               }
               return "";


   }


   //Get Parentx taxonomy term

      public function getTaxonomyParentID($SectionTaxonomy)
   {
            $IsaTaxonomyrQuery = \Drupal::database()->query('SELECT parent_target_id FROM {taxonomy_term__parent} WHERE parent_target_id = :tid', array(':tid' => $SectionTaxonomy))->execute();

            $IsaTaxonomies= $IsaTaxonomyrQuery->fetchAll();
               
               foreach ($IsaTaxonomies as $IsaTaxonomy) {
                 return $IsaTaxonomy->parent_target_id;
               }
               return "";


   }


     //Sending email to ISA Page subscriber
  public function sendCommentEmail($node_id,$email)
  {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $key = 'isa_comment_notification';
        $module = 'isa_comment_notification';

       // $params['message'] = "Dear "\Drupal::request()->getHttpHost()."/node/".$node_id;

        //$params['node_title'] = $label;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

  


                //$subscriber_email = "hunde@ubuntuDS"; 
                $full_link=\Drupal::request()->getHttpHost()."/isp/node/".$node_id;

                $link='<a href="'.$full_link.'">Comment On</a>'; 

                $params['subject'] = "A Comment has been made:";

                $params['message'] = Html::escape("Hello, <p> </p> <p>  A comment has been submitted to the ISA Interoperability Need or page you are assigned to. Please follow the link below to log in and publish the comment.</p> ".$link."<p> </p>Thank You,<br>ISP Team");
                $mailManager->mail($module, $key, $email, $langcode, $params, NULL, $send);
                 
          return 1;
       
  }


}
