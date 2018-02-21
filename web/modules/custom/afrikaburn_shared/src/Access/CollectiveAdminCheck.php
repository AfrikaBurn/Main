<?php

/**
 * @file
 * Contains Collective Admin access checking.
 */

namespace Drupal\afrikaburn_shared\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use \Drupal\user\Entity\User;

/**
 * Checks Collective Admin access.
 */
class CollectiveAdminCheck implements AccessInterface {

  public function appliesTo() {
    return '_is_collective_admin';
  }

  /**
   * Implements access().
   */
  public function access(AccountInterface $account) {

    static $user;
    $uid = \Drupal::currentUser()->id();
    $user = isset($user) ? $user : User::load($uid);
    $node = \Drupal::routeMatch()->getParameter('node');
    $bundle = $node ? $node->bundle() : FALSE;

    $admin = [
      'art' => 'art_admin',
      'performances' => 'art_admin',
      'mutant_vehicles' => 'mutant_vehicle_admin',
      'theme_camps' => 'theme_camp_admin',
    ];

    $wrangler = [
      'art' => 'art_wrangler',
      'performances' => 'art_wrangler',
      'mutant_vehicles' => 'mutant_vehicle_wrangler',
      'theme_camps' => 'theme_camp_wrangler',
    ];

    if ($node && in_array($bundle, array_keys($admin))){
      $field_collective = $node->get('field_collective');
      if ($field_collective) {
        $collective = $field_collective->first()->get('entity')->getTarget();
        return AccessResult::allowedIf(
          $this->isAdmin($uid, $collective) ||
          $user->hasRole('administrator') ||
          $user->hasRole($admin[$bundle]) ||
          $user->hasRole($wrangler[$bundle])
        );
      }
      return AccessResult::allowedIf($user->hasRole('administrator'));
    }

    if ($bundle == 'collective') {
      return
        AccessResult::allowedIf($this->isAdmin($uid, $node) ||
        $user->hasRole('administrator') ||
        isset($admin[$bundle]) && ($user->hasRole($admin[$bundle]) || $user->hasRole($wrangler[$bundle]))
      );
    }

    if ( ($cid = \Drupal::routeMatch()->getParameter('cid')) && (\Drupal::routeMatch()->getParameter('uid')) ){
      $collective = \Drupal::entityTypeManager()->getStorage('node')->load($cid);
      return AccessResult::allowedIf($this->isAdmin($uid, $collective));
    }

    return AccessResult::allowedIf(TRUE);
  }

  /**
   * Checks whether the current user is an admin
   */
  private function isAdmin($uid, $collective){
    $admins = $collective
      ->get('field_col_admins')
      ->referencedEntities();
    foreach ($admins as $admin) {
      if ($admin->id() == $uid){
        return TRUE;
      }
    }
    return FALSE;
  }

}
