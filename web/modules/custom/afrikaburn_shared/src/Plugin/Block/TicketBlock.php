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

    module_load_include('module', 'afrikaburn_shared', 'afrikaburn_shared');

    $settings = Settings::get('afrikaburn.quicket');
    $path_user = \Drupal::routeMatch()->getParameter('user');
    $uid = $path_user ? $path_user->uid->value : \Drupal::currentUser()->id();
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    $quicket_code = $user->get('field_quicket_code')->value;
    $agreements = afrikaburn_shared_agreed($user);

    return [
      '#markup' =>
        $quicket_code
          ? '<div class="messages messages--status">
              <ul>
              Remember to list all kids or teens (anyone under 18) <a href="/user/'.$uid.'/edit">on your profile</a>.
              <p><a href="https://www.quicket.co.za/events/' . $settings['event_id'].'-#/?dc=' . $quicket_code . '" target="_blank" class="button">BUY TICKETS</a></p>
              NOTE: General ticket sales are presently closed. Only Mayday and tickets for minors are currently available. The next round of general ticket sales is on Feb 27 2018.
            </div>'
          : '<div class="messages messages--warning">
              Tickets cannot be bought without an updated profile and the appropriate agreements in place.<br />
              <ul>
                '. ($agreements ? '<li>Have you logged in since creating an account and accepted the T&Cs?</li>' : '') . '
                <li>Have you updated your profile? Click save and correct the errors until it saves.</li>
                <li>Is your profile complete? Check your ID number and date of birth fields.</li>
                <li>Still no joy? <a href="mailto:support@afrikaburn.com">Contact an administrator</a>!</li>
              </ul>
             </div>',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

  }
}
