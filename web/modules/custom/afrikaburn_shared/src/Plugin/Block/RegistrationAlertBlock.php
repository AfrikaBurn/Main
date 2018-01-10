<?php

namespace Drupal\afrikaburn_shared\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;


/**
 * Provides a registration alert block.
 *
 * @Block(
 *   id = "registration_alert_block",
 *   admin_label = @Translation("Registration Alert Block"),
 *   category = @Translation("Registration Alert Block"),
 * )
 */
class RegistrationAlertBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $user = \Drupal::currentUser();
    $collective = \Drupal::routeMatch()->getParameter('node');
    $view = Views::getView('collective_registrations');
    $view->execute('block_1');
    $rows = $view->total_rows;

    return [
      'alert' => $view->total_rows == 0
        ? [
          '#type' => 'markup',
          '#markup' => '<img src="/' . drupal_get_path('module', 'afrikaburn_shared') . '/tribe-project-prompt.png" />',
        ] : [],

      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }
}

