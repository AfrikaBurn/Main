<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration\Controller;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {

  /* ---- Set default languages ---- */

  public static function setLanguage($uid, &$context) {

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    $user->langcode = 'en';
    $user->preferred_langcode = 'en';
    $user->admin_langcode = NULL;

    $context['results'][] = $user->save();
    $context['message'] = 'Setting default languages';      

  }

  /* ---- Set quicket code ---- */

  public static function setQuicket($uid, $code, $id){

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    if ($user) {
      $user->field_quicket_code = $code;
      $user->field_quicket_id = $id;

      $context['results'][] = $user->save();
      $context['message'] = 'Updating quicket codes';      
    }

  }

  /* ---- Finished ---- */

  public static function finished($success, $results, $operations) {

    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One user processed.', '@count users processed.'
      );
    } else {
      $message = t('Finished with errors.');
    }

    drupal_set_message($message);
  }

}