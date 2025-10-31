<?php
/**
 * @file
 * Contains \Drupal\isa_subscription\Form\IsaSubscriptionForm.
 */


namespace Drupal\isa_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Component\Utility\Html;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Messenger;


/**
 * Isa Subscription form.
 */
class IsaSubscriptionForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'subscribers_button_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $anonymous = \Drupal::currentUser()->isAnonymous();
        $node = \Drupal::routeMatch()->getParameter('node');


            if ($node) {

                if (!$anonymous and $node->id() != '776' and $node->bundle() == 'isa_document') {
                    $check_subscription = $this->checkSubscription();
                    if ($check_subscription == 0) {
                        $button_value = "ISA Change Notifications";
                    } else {
                        $button_value = "Unsubscribe";
                    }
                    $form['submit'] = array(
                        '#type' => 'submit',
                        '#value' => t($button_value),
                    );
                    return $form;
                } //Not anonymous and not front page
            }//If node Exists
        }


    /**
     * {@inheritdoc}
     */
    public function checkSubscription()
    {
        $user_id = \Drupal::currentUser()->id();
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node) {
            $node_id = $node->id();
        }

        $database = \Drupal::database();
        $result = $database->query('SELECT COUNT(*) FROM {isa_change_subscription} WHERE uid = :uid AND nid = :nid', array(':uid' => $user_id, ':nid' => $node_id))->fetchField();


        if ($result >= 1) {
            return 1;
        } else {
            return 0;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function sendFirstEmailSubscribers($user_email, $node_title, $node_id)
    {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $key = 'isa_document_updated';
        $module = 'isa_subscription';

        // $params['message'] = "Dear "\Drupal::request()->getHttpHost()."/node/".$node_id;

        //$params['node_title'] = $node_title;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        $subscriber_email = $user_email;


        $full_link = \Drupal::request()->getHttpHost() . "/isp/node/" . $node_id;
        $link = '<a href="' . $full_link . '">' . $node_title . '</a>';


        $params['subject'] = "ISA Change Notification Subscription:" . $node_title;

        $params['message'] = Html::escape("Hello " . \Drupal::currentUser()->getAccountName() . ", <p> </p> <p>  You have been subscribed to the ISA Interoperability Need or page listed below. You will receive an email notification when there are changes made to this page.  You may unsubscribe from these notifications at any time by visiting the page and clicking “unsubscribe” while logged in to the ISP.</p><br>        " . $link . "<p> </p>Thank You,<br>ISP Team");
        $mailManager->mail($module, $key, $subscriber_email, $langcode, $params, NULL, $send);
        return 1;


    }// First subscribe email


    /**
     * {@inheritdoc}
     */
    public function sendFirstEmailUNSubscribers($user_email, $node_title, $node_id)
    {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $key = 'isa_document_updated';
        $module = 'isa_subscription';

        // $params['message'] = "Dear "\Drupal::request()->getHttpHost()."/node/".$node_id;

        //$params['node_title'] = $node_title;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        $subscriber_email = $user_email;


        $full_link = \Drupal::request()->getHttpHost() . "/isp/node/" . $node_id;
        $link = '<a href="' . $full_link . '">' . $node_title . '</a>';

        $params['subject'] = "ISA Change Notification Cancellation:" . $node_title;

        $params['message'] = Html::escape("Hello " . \Drupal::currentUser()->getAccountName() . ",<p> </p> <p>  You have been unsubscribed from the ISA Interoperability Need or page listed below. You will no longer receive email notifications when there are changes made to this page. You may re-subscribe to notifications at any time, by visiting this page and clicking “change notification” while logged in to the ISP. </p>" . $link . "<p> </p> Thank You,<br>ISP Team");
        $mailManager->mail($module, $key, $subscriber_email, $langcode, $params, NULL, $send);
        return 1;


    }//Unsubscribe email


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {


        $user_id = \Drupal::currentUser()->id();
        $node = \Drupal::routeMatch()->getParameter('node');
        $user_email = \Drupal::currentUser()->getEmail();

        if ($node) {
            $node_id = $node->id();
            $node_title = $node->getTitle();
        }

        $check_subscription = $this->checkSubscription();

        $conn = Database::getConnection();

        if ($check_subscription == 0) {

            $conn->insert('isa_change_subscription')->fields(
                array(
                    'uid' => $user_id,
                    'nid' => $node_id,
                )
            )->execute();
            $send_email = $this->sendFirstEmailSubscribers($user_email, $node_title, $node_id);

            $messenger= \Drupal::messenger();
            $messenger->addMessage("Successfully Subscribed ", 'status');
        }//Subscribe action


        else {
            $conn->delete('isa_change_subscription')
                ->condition('uid', $user_id)
                ->condition('nid', $node_id)
                ->execute();

            $send_email = $this->sendFirstEmailUNSubscribers($user_email, $node_title, $node_id);

            $messenger= \Drupal::messenger();
            $messenger->addMessage("Unsubscribed Successfully ", 'status');
        } //Unsubscribe

    }


}
