<?php

namespace Drupal\isa_search_customization\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Class UscdiSearchForm.
 */
class UscdiSearchForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uscdi_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['keyword'] = [
      '#type' => 'textfield',
      '#attributes' => array(
        'placeholder' => t('Search within USCDI'),
      ),
      '#maxlength' => 128,
      '#size' => 30,
      '#required' => true,
      '#required_error' => t('Please enter a keyword to search for'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $keyword = $form_state->getValue('keyword');

    // Redirect to home
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    $search_url = $base_url.'/isp/solr-search/content?f%5B2%5D=select_a_content_area%3AUSCDI&search_api_fulltext='.$keyword;
    $redirected_url = Url::fromUri($search_url);
    $form_state->setRedirectUrl($redirected_url);
  }

}
