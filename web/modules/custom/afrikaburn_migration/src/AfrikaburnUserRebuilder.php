<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {

  public function rebuildUser($uids, &$context) {

    $message = 'Rebuilding Users...';
    $results = array();

    foreach ($uids as $uid) {
      $node = user_load($uid);
      $results[] = $user->save();
    }

    $context['message'] = $message;
    $context['results'] = $results;
  }

  public function finished($success, $results, $operations) {

    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One User processed.', '@count users processed.'
      );
    } else {
      $message = t('Finished with an error.');
    }

    drupal_set_message($message);
  }
}