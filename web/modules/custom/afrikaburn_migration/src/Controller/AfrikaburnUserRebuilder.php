<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration\Controller;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {

  public static function rebuildUser($uid, $agreements, &$context) {

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    // $user->langcode = 'en';
    // $user->preferred_langcode = 'en';
    // $user->admin_langcode = NULL;
    $user->field_agreements = array_keys($agreements);

    // $insert = \Drupal::database()
    //   ->insert('webform_submission')
    //   ->fields(['uuid', 'webform_id', 'uid', 'entity_id']);
    // foreach($agreements as $agreement){
    //   foreach($agreement->field_agreement_terms->referencedEntities() as $webform){
    //     $insert->values(
    //       [
    //         \Drupal::service('uuid')->generate(), 
    //         $webform->id(), 
    //         $user->id(), 
    //         $agreement->id()
    //       ]
    //     );
    //   }
    // }
    // $insert->execute();

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