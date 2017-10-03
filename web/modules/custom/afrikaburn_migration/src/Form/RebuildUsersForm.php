<?php

namespace Drupal\afrikaburn_migration\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Database\Database;


/**
 * Class DeleteNodeForm.
 *
 * @package Drupal\afrikaburn_migration\Form
 */
class RebuildUsersForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'afrikaburn_rebuild_users';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['operation'] = [
      '#type' => 'radios',
      '#options' => [
        'language' => 'Set default languages',
        'quicket' => 'Set quicket info',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Go'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    switch ($form_state->getValues()['operation']){
      case 'language': $this->setLanguage(); break;
      case 'quicket': $this->setQuicket(); break;
      default: return 'Unknown option!';
    }
  }

  /**
   * Sets all user default languages to en
   */
  public function setLanguage(){

    $uids = db_query('SELECT uid FROM {users} WHERE uid != 0')->fetchCol();

    $batch = [
      'title' => t('Setting language to "en" for all users...'),
      'operations' => [],
      'finished' => '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::finished',
    ];

    foreach($uids as $uid){
      $batch['operations'][] = [
        '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::setLanguage',
        [$uid]
      ];
    }

    batch_set($batch);
  }

  /**
   * Sets all user default languages to en
   */
  public function setQuicket(){

    Database::setActiveConnection('migrate');

    $quicket_data = db_query(
      '
        SELECT
          {field_data_field_quicket_id}.entity_id as uid,
          field_quicket_code_value as code,
          field_quicket_id_value as id
        FROM 
          {field_data_field_quicket_code},
          {field_data_field_quicket_id}
        WHERE 
          {field_data_field_quicket_id}.entity_id = {field_data_field_quicket_code}.entity_id
      '
    );

    $batch = [
      'title' => t('Migrating quicket codes...'),
      'operations' => [],
      'finished' => '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::finished',
    ];

    foreach($quicket_data as $values){
      $batch['operations'][] = [
        '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::setQuicket',
        [$values->uid, $values->code, $values->id]
      ];
    }

    Database::setActiveConnection();
    batch_set($batch);
  }
}