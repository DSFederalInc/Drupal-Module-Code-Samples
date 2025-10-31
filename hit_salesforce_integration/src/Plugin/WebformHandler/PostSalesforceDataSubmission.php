<?php

namespace Drupal\hit_salesforce_integration\Plugin\WebformHandler;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


/**
 * Posts a Web Form submission to a remote Sales force URL.
 *
 * @WebformHandler(
 *   id = "post_salesforce_data_submission",
 *   label = @Translation("Post Sales force Data Submission"),
 *   category = @Translation("Custom Healthit"),
 *   description = @Translation("Submit the data to Salesforce Remote URL/API."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */



class PostSalesforceDataSubmission extends WebformHandlerBase {


/**
 * {@inheritdoc}
 */
public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
  $form['submission_url'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Submission URL'),
    '#description' => $this->t('The URL to post the submission data to.'),
    '#default_value' => $this->configuration['submission_url'],
    '#required' => TRUE,
  ];
  return $form;
}

/**
 * {@inheritdoc}
 */
public function defaultConfiguration() {
  return [
    'submission_url' => 'https://webto.salesforce.com/servlet/servlet.WebToLead',
  ];
}

/**
 * {@inheritdoc}
 */
public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  parent::submitConfigurationForm($form, $form_state);
  $this->configuration['submission_url'] = $form_state->getValue('submission_url'); // This will save the configuration  of the submission URL
}

  /**
   * {@inheritdoc}
   */
public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

   $submission_url = $this->configuration['submission_url'];

   $clicked_button = &$form_state->getTriggeringElement()['#parents'][0];



/*
Worst case to handle other submissions
     if($type_of_presentation!="General Educational Session" or $type_of_presentation!="Informal Meeting" or $type_of_presentation!="Panel Discussion"  )
   {
      //Then other is selected from our end
       $type_of_presentation_value="'00Nt0000000KMnK' => $webform_submission->getData('type_of_presentation')";
   }
   else
   {
   $type_of_presentation_value="'00Na000000BLZM2' => $webform_submission->getData('type_of_presentation')";
   }

*/



  if ($clicked_button == 'submit') {
      $values = $webform_submission->getData();

      // requested speaker
      $requested_speaker_first_name = $values['required_speaker_name']['first'];
      $requested_speaker_last_name = $values['required_speaker_name']['last'];
      $is_your_preferrer_speaker = $values['is_your_preferred_speaker_assistant_secretary_tripathi'];
      $is_an_award_certificate_gift_or = $values['is_an_award_certificate_gift_or_other_item_being_presented_to_th'];
      $cost_per_person_to_host = $values['if_there_is_no_publicly_available_ticket_price_or_registration_f'];
      $is_there_a_cost_for_attendance = $values['is_there_a_cost_for_attendance_for_members_of_the_general_public'];
      $if_yes_please_describe = $values['if_yes_please_describe_gift'];
      // $are_any_govt_empl_participating = $values['are_any_other_government_employees_participating_in_this_event'];
      // $if_so_please_name_and_describe = $values['if_so_please_name_and_describe_their_participation'];
      $will_the_speakers_partici_be_recorded = $values['will_the_employee_s_participation_be_recorded_or_transcribed'];
      $exact_amount_of_ticket_or_registration_fee = $values['what_is_the_cost_of_attendance_for_a_member_of_the_general_publi'];
      $is_speaking_date_flexible = $values['is_the_speaking_date_flexible'];
      $is_this_event = $values['is_this_event'];
      $if_requested_speaker_is_not_avail = $values['if_the_requested_speaker_is_not_available_are_you_interested_in_'];
      $list_other_speakers_interest = $values['list_other_speakers_interested_in_inviting'];
      $please_list_other_date = $values['please_list_other_date_options'];
      $majority_accopanied_by_guest = $values['will_the_majority_of_participants_be_accompanied_by_a_guest'];
      $name_and_describe_their_participation = $values['if_so_please_name_and_describe_their_participation'];
      $other_govt_employees_partici = $values['are_any_other_government_employees_participating_in_this_event'];
      // $requestor_cell_phone = $values['office_phone'];
      $promotional_materials_include_name = $values['will_any_promotional_materials_for_the_event_include_the_employe'];
      $separately_ticketed_portions_not_in_the_fee = $values['are_there_any_separately_ticketed_portions_of_this_event_or_meal'];
      $list_additional_speakers_invited = $values['please_list_additional_speakers_you_ve_invited_to_this_session_i'];
      $event_website_if_available = $values['event_url_if_available'];
      // $types_of_presentation = $values['type_of_presentation'];
      // $what_topics_requested_speaker_discuss = $values['what_topics_will_the_requested_speaker_be_discussing_please_prov'];
      // $is_interested_in_another_speaker = $values['if_the_requested_speaker_is_not_available_are_you_interested_in_'];
      // $second_speaker_first_name = $values['second_speaker_name']['first'];
      // $second_speaker_last_name = $values['second_speaker_name']['last'];


      // event date and time
      $date_speaking_engagement = strtotime($values['date_of_speaking_engagement']);
      $date_speaking_engagement = date('n/j/Y', $date_speaking_engagement);
      $event_start_time = $values['start_time'];
      $event_end_time = $values['end_time'];
      $time_zone = $values['event_time'];
      $response_time = strtotime($values['response_to_request_needed_by']);
      $response_time = date('n/j/Y', $response_time);



      // presentation topics and format
      $presentation_type = $values['type_of_presentation'];
      $other_presentation_type = '';
      if(!empty($presentation_type)) {
          if(!in_array($presentation_type, ['General Educational Session', 'Informal Meeting', 'Keynote', 'Panel Discussion'])) {
              $presentation_type = 'Other';
              $other_presentation_type = $values['type_of_presentation'];
          }
      }
      $is_slides_required = $values['will_slides_be_required_for_the_presentation_'];
      $slides_deadline = strtotime($values['slide_deadline']);
      $slides_deadline = date('n/j/Y', $slides_deadline);
      $presentation_expected_attendance = $values['expected_attendance_at_presentation'];
      $discussion_topic = $values['what_topics_will_the_requested_speaker_be_discussing_please_prov'];
      $agenda_link = $values['please_provide_a_link_to_the_event_agenda'];


      // event information
      $event_full_name = $values['full_name_of_the_event_no_acronym_'];
      $event_organization_url = $values['event_url'];
      $event_background = $values['event_background'];
      $event_expected_attendance = $values['expected_event_attendance'];
      // $array_type_event_attendee_background = $values['event_attendee_background2'];
      $event_attendee_background = $values['event_attendee_background2'];
      // \Drupal::logger('hit_salesforce_integration')->notice(json_encode($event_attendee_background));
      $event_attendee_background_other = '';
      if(!empty($event_attendee_background)) {
        // \Drupal::logger('hit_salesforce_integration')->notice(json_encode($event_attendee_background));
          if(!array_intersect($event_attendee_background,
            ["Providers i.e., physicians, nurses, etc.",
            "Patients",
            "Vendors",
            "Government Health IT",
            "CIO's/CMIO's",
            "Consumers"])) {
              // \Drupal::logger('hit_salesforce_integration')->notice(json_encode($event_attendee_background));
              $event_attendee_background = 'Other';
              $event_attendee_background_other = $values['event_attendee_background2'];
          } else {
            $event_attendee_background = implode(";", $values['event_attendee_background2']);
          }
      }
      $is_other_speaker = $values['other_confirmed_speakers_at_this_event'];
      $other_speaker_list = $values['please_list'];
      $is_media = $values['will_media_be_present_at_the_event_'];
      $media_list = $values['please_list_all_media_invited'];
      $is_onc_spoken_past_year = $values['onc_spoken_at_event_in_past_year_'];
      $past_onc_speakers = $values['please_specify_onc_speakers_and_the_dates_they_attended'];



      // event location
      $is_multiple_location = $values['multiple_venue_locations_for_this_event_'];
      $list_multiple_venue = $values['list_multiple_venues'];
      $is_remote_participation = $values['is_remote_participation_available'];
      $venue_name = $values['venue_name_1'];
      $venue_address_1 = $values['venue_name']['address'];
      $venue_address_2 = $values['venue_name']['address_2'];
      $venue_address_city = $values['venue_name']['city'];
      $venue_address_state = $values['venue_name']['state_province'];
      $venue_address_zip = $values['venue_name']['postal_code'];
      $venue_address_country = $values['venue_name']['country'];



      // event host information
      $organization_name = $values['organization_name_no_acronym_'];
      $organization_url = $values['organization_url'];
      $organization_type = $values['type_of_organization'];
      $organization_type_other = '';
      if(!empty($organization_type)) {
          if(!in_array($organization_type, ['Academy Entity',
              'Association',
              'Community Health Center Clinic',
              'Consulting or Law Firm',
              'Federal Government',
              'Long Term Care Facility',
              'State Government',
              'Hospital, Multi-Hospital System, Integrated Delivery System',
              'Public Health Facility',
              'Physician Organization',
              'Vendor',
              'Not for Profit'])) {
              $organization_type = 'Other';
              $organization_type_other = $values['type_of_organization'];
          }
      }
      $organization_facebook = $values['organization_facebook_page'];
      $organization_twitter = $values['organization_twitter_handle'];
      $organization_recent_news = $values['url_of_recent_host_organization_s_news_and_media_coverage'];
      $is_co_sponsor = $values['does_this_event_have_any_co_sponsors_'];
      $co_sponsor = $values['please_specify_event_co_sponsors'];



      // requestor contact information
      $requestor_title = $values['requestor_contact_name']['title'];
      $requestor_first_name = $values['requestor_contact_name']['first'];
      $requestor_last_name = $values['requestor_contact_name']['last'];
      $requestor_email = $values['email_address'];
      $requestor_office_phone = $values['office_phone'];
      $requestor_cell = $values['cell_phone'];

  //$submission_url=$webform_submission->getData('submission_url');


  try {
  $client = new \GuzzleHttp\Client();
  
  $formData = [
    'form_params' => [
    'recordType' => '012a0000001WIlo',
    'Status' => 'New Lead',
    'oid' => '00D30000000jdv4',
    'retURL' => 'https://www.healthit.gov/speaker-request-thank-you',
    '00Na000000BLZLq' => $requested_speaker_first_name,
    '00Na000000BLZLr' => $requested_speaker_last_name,
    '00NSJ0000049OaH' => $is_your_preferrer_speaker,
    '00NSJ0000049QU1' => $is_an_award_certificate_gift_or,
    '00NSJ000004Bkur' => $exact_amount_of_ticket_or_registration_fee,
    '00NSJ0000049Reb' => $cost_per_person_to_host,
    '00NSJ0000049PPt' => $is_speaking_date_flexible,
    '00NSJ0000049RJd' => $is_this_event,
    '00NSJ0000049RTJ' => $is_there_a_cost_for_attendance,
    '00NSJ000004FKlh' => $list_other_speakers_interest,
    '00NSJ000004AwBh' => $please_list_other_date,
    '00NSJ0000049SkL' => $majority_accopanied_by_guest,
    '00NSJ000004BlUL' => $name_and_describe_their_participation,
    '00NSJ0000049Pcn' => $organization_url,
    '00NSJ0000049Qll' => $other_govt_employees_partici,
    '00NSJ0000049R0H' => $will_the_speakers_partici_be_recorded,
    '00NSJ0000049REn' => $promotional_materials_include_name,
    '00Nt0000000XlLY' => $is_remote_participation,
    '00NSJ0000049SXR' => $separately_ticketed_portions_not_in_the_fee,
    '00NSJ000004AvSX' => $list_additional_speakers_invited,
    '00NSJ000004Ig89' => $if_yes_please_describe,
    // '00Na000000BLZLo' => $is_interested_in_another_speaker,
    // '00Na000000BLZLt' => $second_speaker_first_name,
    // '00Na000000BLZLu' => $second_speaker_last_name,
    '00Na000000BLZLF' => $date_speaking_engagement,
    '00Na000000BLZLP' => $event_start_time,
    '00Na000000BLZLL' => $event_end_time,
    '00Na000000BLZLQ' => $time_zone,
    // '00Na000000BLZLp' => $response_time,
    '00Na000000BLZM2' => $presentation_type,
    '00Nt0000000KMnK' => $other_presentation_type,
    // '00Na000000BLZLw' => $is_slides_required,
    // '00Nf0000000wFy7' => $slides_deadline,
    // '00Na000000BLZLU' => $presentation_expected_attendance,
    '00Na000000BLZLz' => $discussion_topic,
      // '00Nt0000000XlLU' => $agenda_link,
        '00Na000000BLZLe' => $event_full_name,
    '00Na000000BLZLR' => $event_website_if_available,
    '00Na000000BLZLJ' => $event_background,
    '00Na000000BLZLM' => $event_expected_attendance,
    '00Na000000BLZLH' => $event_attendee_background,
    '00Na000000BLZLI' => $event_attendee_background_other,
    '00Na000000BLZLk' => $if_requested_speaker_is_not_avail,
    // '00Nf0000000wFyv' => $other_speaker_list,
    '00Na000000BLZLb' => $is_media,
    // '00Na000000BLZL9' => $media_list,
    // '00Nt0000000XlLV' => $is_onc_spoken_past_year,
    //'00Nf0000000wZHw' => $past_onc_speakers,
      // '00Na000000BLZLf' => $past_onc_speakers,
    // '00Nt0000000XlLX' => $is_multiple_location,
    // '00Nt0000000XlLW' => $list_multiple_venue,
        '00Na000000BLZM7' => $venue_name,
        '00Na000000BLZM3' => $venue_address_1,
        '00Na000000BLZM4' => $venue_address_2,
        '00Na000000BLZM6' => $venue_address_country,
        '00Na000000BLZM8' => $venue_address_state,
        '00Na000000BLZM5' => $venue_address_city,
        '00Na000000BLZM9' => $venue_address_zip,
        'company' => $organization_name,
    // 'URL' => $organization_url,
    '00Na000000BLZM0' => $organization_type,
    '00Na000000BLZM1' => $organization_type_other,
    // '00Na000000BLZLh' => $organization_facebook,
    // '00Na000000BLZLj' => $organization_twitter,
    // '00Na000000BLZLW' => $organization_recent_news,
    //'00Nf0000000wZDa' => $is_co_sponsor,
      '00Na000000BLZLs' => $requestor_office_phone,
    // '00Na000000BLZLl' => $co_sponsor,
    '00Na000000BLa9X' => $requestor_first_name,
    '00Na000000BLa9Y' => $requestor_last_name,
    'title' => $requestor_title,
        'first_name' =>  $requestor_first_name,
        'last_name' =>  $requestor_last_name,
        'email' =>  $requestor_email,
        'phone' => $requestor_office_phone,
        '00Nf0000000waSg' => $requestor_cell,
    ]];
    \Drupal::logger('hit_salesforce_integration')->notice(json_encode($formData));
    $response = $client->request('POST', $submission_url ,  $formData);


  $code = $response->getStatusCode();


  if ($code >= 400 || $code === 0) {
    // Handle the error
    echo "Error while trying to create request";
//    exit();

  }
  }

    catch (RequestException $e) {
  // Log the error with details
      \Drupal::logger('hit_salesforce_integration')->error(
        'Failed to submit to Salesforce: @message. Response: @response',
        [
          '@message' => $e->getMessage(),
          '@response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
        ]
      );

    return FALSE;
  }

 }// If  the clicked button  is submit , this will protect the form not being submitted for every next page.
}

}
