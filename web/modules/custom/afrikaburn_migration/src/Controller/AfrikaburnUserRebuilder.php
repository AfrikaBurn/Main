<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration\Controller;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {

  public static function rebuildUser($user, &$context) {

    $user->langcode = 'en';
    $user->preferred_langcode = 'en';
    $user->admin_langcode = NULL;

    $context['results'][] = $user->save();
    $context['message'] = 'Rebuilding Users';

  }

  public static function finished($success, $results, $operations) {

    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One User processed.', '@count users rebuilt.'
      );
    } else {
      $message = t('Finished with an error.');
    }

    drupal_set_message($message);
  }

}