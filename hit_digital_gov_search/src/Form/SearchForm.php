<?php
/**
 * @file
 * Contains \Drupal\hit_digital_gov_search\src\Form\SearchForm.
 */
namespace Drupal\hit_digital_gov_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;

class SearchForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  protected $site_name;

  public function getFormId() {
    return 'search_form';
  }

  public function getSiteNameHost() {
    return \Drupal::request()->getHost();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = \Drupal::config('hit_digital_gov_search.settings');
    $affiliate = $config->get('search_affiliate');
    
    $form['query'] = [
      '#type' => 'search',
      '#title' => $this->t('Search'),
      '#title_display' => 'invisible',
      '#default_value' => '',
      '#attributes' => [
        'id' => 'query',
        'title' => $this->t('Enter the terms you wish to search for.'),
        'placeholder' => $this->t('Search'),
        'class' => [
          'usagov-search-autocomplete'
          ],
      ],
      '#autocomplete_route_name' => 'hit_digital_gov_search.autocomplete',
    ];
    $form['affiliate'] = [
      '#type' => 'hidden',
      '#value' => $affiliate,
      '#name' => 'affiliate',
      '#attributes' => [
        'id' => 'affiliate',
        ]
    ];
    $form['utf8'] = [
      '#name' => 'utf8',
      '#type' => 'hidden',
      '#value' => '%E2%9C%93',
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
        '#value' => $this->t('Search'),
      '#button_type' => 'primary',
        "#id" => 'hit-search-button',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $keywords = str_replace(' ', '+', $form_state->getValue('query'));
    return $form_state->setResponse(new TrustedRedirectResponse('https://search.usa.gov/search?utf8=%E2%9C%93&affiliate=www.healthit.gov&query=' . $keywords));
  }
}
