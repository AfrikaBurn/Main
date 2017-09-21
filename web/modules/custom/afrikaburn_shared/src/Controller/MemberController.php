<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_shared\MemberController.
 */

namespace Drupal\afrikaburn_shared\Controller;


use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class MemberController extends ControllerBase {

  /**
   * Invite a person to the group
   */
  public static function invite($cid = FALSE, $email = FALSE) {
    $collective = \Drupal::entityTypeManager()->getStorage('node')->load($cid);
    if ($collective->bundle() == 'collective'){
      $collective->get('field_col_invitee')->appendItem($email);
    }
  }

  /**
   * Remove a member from a group
   */
  public static function boot($cid = FALSE, $uid = FALSE) {

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
    $collective = \Drupal::entityTypeManager()->getStorage('node')->load($cid);
    $member_index = self::memberIndex($uid, $collective);

    self::removeFromMembers($member_index, $collective);

    drupal_set_message(
      t(
        '%user has been booted from %collective', 
        [
            '%user' => $user->name, 
            '%collective' => $collective->getTitle(),
        ]
      ),
      'status',
      TRUE
    );
    $redirect = new RedirectResponse('/node/' . $cid);
    $redirect->send();
  }

  /**
   * Promote member to admin
   */
  public static function admin($cid = FALSE, $user = FALSE) {
    $collective = \Drupal::entityTypeManager()->getStorage('node')->load($cid);
    if ($collective->bundle() == 'collective'){
      $collective->get('field_col_admins')->appendItem($email);
    }    
  }

  /**
   * Accept a group invitation
   */
  public static function accept($cid = FALSE) {

    if (\Drupal::currentUser()->isAnonymous()){
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

    $nid = \Drupal::routeMatch()->getParameter('nid');
    $collective = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($collective->bundle() == 'collective'){

      $uid = \Drupal::currentUser()->id();
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      $inviteIndex = self::inviteIndex($user, $collective);

      if (count($inviteIndex)) {

        self::addToMembers($user, $collective);
        self::removeFromInvites($inviteIndex, $collective);
        $collective->save();

      } else {
        return array(
          '#type' => 'markup',
          '#markup' => 'Oh no! This invitation is meant for a different email address! Make sure you get invited to collectives with this email address:<br />' . $user->getEmail(),
        );
      }

    } else {
      return array(
        '#type' => 'markup',
        '#markup' => t('Oh no! It seems that this group no longer exists!'),
      );
    }

    $redirect = new RedirectResponse('/node/' . $nid);
    $redirect->send();
  }

  /**
   * Ignore a group invitation
   */
  public static function ignore($cid = FALSE) {

    if (\Drupal::currentUser()->isAnonymous()){
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

    $nid = \Drupal::routeMatch()->getParameter('nid');
    $collective = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($collective->bundle() == 'collective'){

      $uid = \Drupal::currentUser()->id();
      $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);
      $inviteIndex = self::inviteIndex($user, $collective);

      if (count($inviteIndex)) {

        self::removeFromInvites($inviteIndex, $collective);
        $collective->save();

      } else {
        return array(
          '#type' => 'markup',
          '#markup' => 'Oh no! This invitation is meant for a different email address! Make sure you get invited to collectives with this email address:<br />' . $user->getEmail(),
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

  /* ---- Utility ---- */

  /**
   * Returns the index(es) of a users invite.
   */
  private static function inviteIndex($user, $collective) {

    $indexes = [];
    $field_col_invitee = $collective->get('field_col_invitee');

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

  /**
   * Returns the index(es) of a users invite.
   */
  private static function memberIndex($user, $collective) {

    $indexes = [];
    $field_col_members = $collective->get('field_col_members');

    if ($field_col_members){

      $members = array_column(
        $field_col_members->getValue(),
        'target_id'
      );

      $indexes = [];
      foreach($members as $index=>$mid){
        if ($mid == $uid) {
          $indexes[$index] = $index;
        }
      }
    }

    return $indexes;
  }

  // Add user to members
  private static function addToMembers($user, $collective) {
    $collective->get('field_col_members')->appendItem($user);
  }

  // Remove email address(es) from invites
  private static function removeFromInvites($inviteIndexes, $collective) {
    foreach(array_reverse($inviteIndexes) as $index){
      $collective->get('field_col_invitee')->removeItem($index);    
    }
  }

  // Remove user from members
  private static function removeFromMembers($inviteIndexes, $collective) {
    foreach(array_reverse($inviteIndexes) as $index){
      $collective->get('field_col_invitee')->removeItem($index);    
    }
  }

}

