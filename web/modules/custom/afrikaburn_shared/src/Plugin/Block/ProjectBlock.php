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

    $block = [
      '#type' => 'markup',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node) { 

      $nid = $node->id();
      $block['#markup'] = '
        <h2>Register</h2>
        <ul>
          <li><a href="/node/add/art?field_collective=' . $nid . '" class="button">An artwork</a></li>        
          <li><a href="/node/add/performances?field_collective=' . $nid . '" class="button">A Performance</a></li>
          <li><a href="/node/add/theme_camps?field_collective=' . $nid . '" class="button">A Theme camp</a></li>
        </ul>
      ';
    }

    return $block;
  }
}