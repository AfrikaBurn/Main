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
    $uid = $path_user ? $path_user->uid->value : \Drupal::currentUser()->id();
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    $quicket_code = $user->get('field_quicket_code')->value;

    return [
      '#markup' =>
        $quicket_code
          ? '<div class="messages messages--status">
              <ul>
              Remeber to list all kids or teens (any minors) coming with you, <a href="/user/'.$uid.'/edit"> on your profile</a> <em>before</em> you:
              <p><a href="https://www.quicket.co.za/events/' . $settings['event_id'].'-#/?dc=' . $quicket_code . '" target="_blank" class="button">BUY TICKETS</a></p>
              If you\'re having trouble with the link to buy tickets, use this code on <a href="https://www.quicket.co.za">www.Quicket.co.za</a>: ' . $quicket_code . '
            </div>'
          : '<div class="messages messages--warning">
              Tickets cannot be bought without an updated profile and the appropriate agreements in place.<br />
              <ul>
                <li>Is your profile complete? Check the ID number and date of birth fields.</li>
                <li>Have you logged in since creating an account?</li>
                <li>If you have been presented with terms and conditions, did you accept them?</li>
                <li>If you have been presented with the Introduction or Updates to how we do stuff, have you read and accepted them?</li>
                <li>Have you updated your profile?</li>
                <li>Still no joy? <a href="mailto:support@afrikaburn.com">Contact an administrator</a>!</li>
              </ul>
             </div>',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

  }
}
