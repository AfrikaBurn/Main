<?php

namespace Drupal\afrikaburn_migrate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RebuildUsersForm.
 *
 * @package Drupal\afrikaburn_migrate\Form
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
    $form['delete_node'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Rebuild Users'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $uids = db_query('SELECT uid FROM {users} WHERE uid != 0')->fetchCol();

    $batch = array(
      'title' => t('Rebuilding Users...'),
      'operations' => [],
      'finished' => '\Drupal\afrikaburn_migration\AfrikaburnUserRebuilder::finished',
    );

    foreach($uids as $uid){
      $batch['operations'] = [
        '\Drupal\afrikaburn_migration\AfrikaburnUserRebuilder::rebuildUser',
        [$uid]
      ];
    }

    batch_set($batch);
  }
}