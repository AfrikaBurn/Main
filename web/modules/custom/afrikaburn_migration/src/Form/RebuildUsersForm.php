<?php

namespace Drupal\afrikaburn_migrate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RebuildUsersForm.
 *
 * @package Drupal\batch_example\Form
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

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->sort('created', 'ASC')
      ->execute();

    $batch = array(
      'title' => t('Deleting Node...'),
      'operations' => array(
        array(
          '\Drupal\batch_example\DeleteNode::deleteNodeExample',
          array($nids)
        ),
      ),
      'finished' => '\Drupal\batch_example\DeleteNode::deleteNodeExampleFinishedCallback',
    );

    batch_set($batch);
  }
}