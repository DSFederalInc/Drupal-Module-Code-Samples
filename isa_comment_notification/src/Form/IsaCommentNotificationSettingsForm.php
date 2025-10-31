<?php

namespace Drupal\isa_comment_notification\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class IsaCommentNotificationSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['isa_comment_notification.settings'];
  }

  public function getFormId() {
    return 'isa_comment_notification_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('isa_comment_notification.settings');

    $form['default_notification_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Default Notification Email'),
      '#description' => $this->t('Email address to use when no assigned user is found.'),
      '#default_value' => $config->get('default_notification_email'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('isa_comment_notification.settings')
      ->set('default_notification_email', $form_state->getValue('default_notification_email'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}