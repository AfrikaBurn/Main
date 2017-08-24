<?php

namespace Drupal\afrikaburn_emails;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'afrikaburn_emails_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'afrikaburn_emails.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
  
    $config = $this->config('afrikaburn_emails.settings');
    $user = \Drupal::currentUser();
    $message_definition = $config->get('message_definition');

    $form['message_definition'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message definition'),
      '#description' => "List one definition per line in the format:<br />
        operation:entity_type[:bundle]|Subject|recipient,recipient,...<br />
        Where:<br />
        operation = [create|update|delete]<br />
        entity_type = [node|user|...]<br />
        bundle = [theme_camp|mutant_vehicle|...]<br />
        recipients = [author|group|wrangler|...]<br />
        Eg.<br />
        update:user|Your account has been changed|author<br />
        create:node:page|A new page has been created|group",
      '#default_value' => $message_definition,
    ];

    $form[] = [
      '#markup' => '<h3>Message templates</h3>',
    ];

    if (strlen($message_definition)){
      $definition_pairs = explode("\n", $message_definition);
      if (is_array($definition_pairs)){
        foreach($definition_pairs as $key_label){
          list($key, $label, $recipient) = explode('|', $key_label);
          $parts = explode(':', $key);
          $form[$key] = [
            '#type' => 'textarea',
            '#title' => $label,
            '#default_value' => $config->get($key),
            '#attributes' => [
              'rows' => 20,
            ],
            '#access' => $user->hasPermission('edit ' . $key . ' template'),
            '#description' => 'Available tokens: [' . $parts[1] . ':...]',
          ];
        }
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('afrikaburn_emails.settings')
      ->set('message_definition', $values['message_definition'])
      ->save();
    $definition_pairs = explode("\n", $values['message_definition']);
    if (is_array($definition_pairs)){
      foreach($definition_pairs as $key_label){
        list($key, $label) = explode('|', $key_label);
        $this->config('afrikaburn_emails.settings')
          ->set($key, $values[$key])
          ->save();
      }
    }
  }

}