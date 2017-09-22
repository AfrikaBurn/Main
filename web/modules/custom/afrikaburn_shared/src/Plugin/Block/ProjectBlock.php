<?php

namespace Drupal\afrikaburn_shared\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "project_block",
 *   admin_label = @Translation("Project Block"),
 *   category = @Translation("Project Block"),
 * )
 */
class ProjectBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $user = \Drupal::currentUser();
    $collective = \Drupal::routeMatch()->getParameter('node');
    if ($collective && $collective->bundle() == 'collective') {
      $cid = $collective->id();
    }

    dpm($cid);

    return $cid && \Drupal::service('access_manager')->checkNamedRoute('afrikaburn_shared.admin', ['cid' => $cid], $user)
      ? [
        '#type' => 'markup',
        '#markup' => '
          <h2>Register</h2>
          <ul>
            <li><a href="/node/add/art?field_collective=' . $cid . '" class="button">An artwork</a></li>        
            <li><a href="/node/add/performances?field_collective=' . $cid . '" class="button">A Performance</a></li>
            <li><a href="/node/add/theme_camps?field_collective=' . $cid . '" class="button">A Theme camp</a></li>
          </ul>
        ',
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
