<?php

namespace Drupal\afrikaburn_shared\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "project_links_block",
 *   admin_label = @Translation("Project Links Block"),
 *   category = @Translation("Project Links Block"),
 * )
 */
class ProjectLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $user = \Drupal::currentUser();
    $collective = \Drupal::routeMatch()->getParameter('node');
    $cid = $collective && $collective->bundle() == 'collective'
      ? $collective->id()
      : FALSE;

    return $cid && \Drupal::service('access_manager')->checkNamedRoute('afrikaburn_shared.admin', ['cid' => $cid], $user)
      ? [
        '#type' => 'view',
        '#name' => 'collective_registrations',
        '#display_id' => \Drupal::service('access_manager')->checkNamedRoute('afrikaburn_shared.admin', ['cid' => $collective->id()], $user)
          ? 'registration_edit_block'
          : 'registration_view_block',

        '#cache' => [
          'max-age' => 0,
        ]
      ]
      : [
        '#cache' => [
          'max-age' => 0,
        ]
      ];
  }

}
