<?php

namespace Drupal\cs_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class CSFormConfigForm extends ConfigFormBase {

  public function getFormId() {
    return 'cs_form_config_form';
  }

  protected function getEditableConfigNames() {
    return ['cs_form.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cs_form.settings');

    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key'),
      '#description' => $this->t('Enter Campaign Monitor API Key.'),
      '#maxlength' => 256,
    ];

    $form['list_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('List ID'),
      '#default_value' => $config->get('list_id'),
      '#description' => $this->t('Enter Campaign Monitor List ID.'),
    ];

    $form['success_page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Success Page'),
      '#default_value' => $config->get('success_page'),
      '#description' => $this->t('Enter success submission page (eg. /thank-you)'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('cs_form.settings');

    $config->set('api_key', $form_state->getValue('api_key'));
    $config->set('list_id', $form_state->getValue('list_id'));
    $config->set('success_page', $form_state->getValue('success_page'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
