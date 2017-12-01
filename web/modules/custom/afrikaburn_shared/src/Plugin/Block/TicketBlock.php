<?php

namespace Drupal\afrikaburn_shared\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \Drupal\Core\Site\Settings;

/**
 * Provides a group invitation block.
 *
 * @Block(
 *   id = "ticket_block",
 *   admin_label = @Translation("Ticket Block"),
 *   category = @Translation("Ticket Block"),
 * )
 */
class TicketBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $settings = Settings::get('afrikaburn.quicket');

    $path_user = \Drupal::routeMatch()->getParameter('user');
    $user = $path_user
      ? \Drupal::entityTypeManager()->getStorage('user')->load($path_user->uid->value)
      : \Drupal::entityTypeManager()->getStorage('user')->load(\Drupal::currentUser()->id());
    $quicket_code = $user->get('field_quicket_code')->value;

    return [
      '#markup' =>
        $quicket_code
          ? '<div class="messages messages--status">Buy your ticket using this code: <a href="https://www.quicket.co.za/events/'.$settings['event_id'].'-#/?dc=' . $quicket_code . '" target="_blank">'.$quicket_code.'</a></div>'
          : '<div class="messages messages--warning">Tickets cannot be bought without the appropriate agreements in place</div>',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

  }
}
