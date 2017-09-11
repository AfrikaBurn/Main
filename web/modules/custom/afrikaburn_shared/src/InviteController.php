<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_shared\InviteController.
 */

namespace Drupal\afrikaburn_shared;


use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class InviteController extends ControllerBase {

  /**
   * Accept a group invitation
   */
  public static function accept() {

    $nid = \Drupal::routeMatch()->getParameter('nid');
    $group = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($group->bundle() == 'collective'){

      $uid = \Drupal::currentUser()->id();
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      $inviteIndex = self::inviteIndex($user, $group);

      if (count($inviteIndex)) {
        self::addToMembers($user, $group);
        self::removeFromInvites($inviteIndex, $group);
        $group->save();

      } else {
        return array(
          '#type' => 'markup',
          '#markup' => t('Oh no! It seems like your invitation expired!'),
        );
      }

    } else {
      return array(
        '#type' => 'markup',
        '#markup' => t('Oh no! It seems that group no longer exists!'),
      );
    }

    $redirect = new RedirectResponse('/node/' . $nid);
    $redirect->send();
  }

  /**
   * Ignore a group invitation
   */
  public static function ignore() {

    $nid = \Drupal::routeMatch()->getParameter('nid');
    $group = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($group->bundle() == 'collective'){

      $uid = \Drupal::currentUser()->id();
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      $inviteIndex = self::inviteIndex($user, $group);

      if (count($inviteIndex)) {
        self::removeFromInvites($inviteIndex, $group);
        $group->save();
      } else {
        return array(
          '#type' => 'markup',
          '#markup' => t('Oh well! It seems like your invitation expired anyway!'),
        );
      }

    } else {
      return array(
        '#type' => 'markup',
        '#markup' => t('Oh well! It seems that group no longer exists anyway!'),
      );
    }

    $redirect = new RedirectResponse('/');
    $redirect->send();
  }

  /* ---- Utility ----*/

  /**
   * Returns the index(es) of a users invite.
   */
  private static function inviteIndex($user, $group) {

    $indexes = [];
    $field_col_invitee = $group->get('field_col_invitee');

    if ($field_col_invitee){

      $invitees = array_column(
        $field_col_invitee->getValue(),
        'value'
      );

      $mails = [$user->get('mail')->getValue(), $user->get('field_secondary_mail')->getValue()];
      foreach($mails as $index=>$mail){
        if ($mail) {
          $value = array_values(array_column($mail, 'value'))[0];
          if (in_array($value, $invitees)) {
            $indexes[$index] = $index;
          }
        }
      }
    }

    return $indexes;
  }

  // Add user to members
  private static function addToMembers($user, $group) {
    $group->get('field_col_members')->appendItem($user);
  }

  // Remove email address(es) from invites
  private static function removeFromInvites($inviteIndex, $group) {
    foreach(array_reverse($inviteIndex) as $index){
      $group->get('field_col_invitee')->removeItem($index);    
    }
  }
}

