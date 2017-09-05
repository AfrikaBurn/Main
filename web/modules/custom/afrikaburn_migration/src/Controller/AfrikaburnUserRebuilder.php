<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration\Controller;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {

  public function rebuildUser($uid, &$context) {

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);;
    $user->langcode = 'en';
    $user->preferred_langcode = 'en';
    $user->admin_langcode = NULL;

    try{
      $context['results'][] = $user->save();
    } catch (Exception $e) {
      $context['message'] = 'Oops';
    }

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