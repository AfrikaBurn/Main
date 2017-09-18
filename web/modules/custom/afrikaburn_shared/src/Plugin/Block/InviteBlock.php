<?php

namespace Drupal\afrikaburn_shared\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a group invitation block.
 *
 * @Block(
 *   id = "invite_block",
 *   admin_label = @Translation("Invite Block"),
 *   category = @Translation("Invite Block"),
 * )
 */
class InviteBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    $form = \Drupal::service('entity.manager')
      ->getFormObject('node', 'member_invitation')
      ->setEntity($node);
    $build = \Drupal::formBuilder()->getForm($form);

    $build['revision']['#access'] = FALSE;
    $build['revision_information']['#access'] = FALSE;
    $build['revision_log']['#access'] = FALSE;

    unset($build['actions']['delete']);

    return $build;
  }
}
