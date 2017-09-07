<?php

namespace Drupal\afrikaburn_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
    $aids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'agreement')
      ->condition('title', ['Introduction', 'Terms & Conditions'], 'IN')
      ->execute();

    $agreements = entity_load_multiple('node', $aids);

    $batch = array(
      'title' => t('Rebuilding Users...'),
      'operations' => [],
      'finished' => '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::finished',
    );

    foreach($uids as $uid){
      $batch['operations'][] = [
        '\Drupal\afrikaburn_migration\Controller\AfrikaburnUserRebuilder::rebuildUser',
        [$uid, $agreements]
      ];
    }

    batch_set($batch);
  }
}