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

  public static function setQuicket($uid, $code, $id, &$context){

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    if ($user) {

      $user->field_quicket_code = $code;
      $user->field_quicket_id = $id;

      $context['results'][] = $user->save();
      $context['message'] = 'Updating quicket codes';      
    }

  }

  /* ---- Set updated agreement ---- */

  public static function setAgreementUpdate($uid, $agreement, &$context){

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    if ($user) {

      if ($user->get('field_agreements')->count() == 0){
        $user->get('field_agreements')->appendItem($agreement);
        $context['results'][] = $user->save();
      }

      $context['message'] = 'Attaching updated agreement';
    }

  }

  /* ---- Set updated agreement ---- */

  public static function getNewQuicketInfo($uid, $agreement, &$context){

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    if ($user) {
    
      module_load_include('inc', 'afrikaburn_shared', 'includes/quicket');
    
      $context['results'][] = _quicket_new($user);
      $context['message'] = 'Attaching updated agreement';      
    }

  }

  /* ---- Finished ---- */

  public static function finished($success, $results, $operations) {

    drupal_set_message(
      $success
        ? \Drupal::translation()->formatPlural(
            count($results),
            'One user processed.', '@count users processed.'
          )
        : t('Finished with errors.')
    );

  }

}