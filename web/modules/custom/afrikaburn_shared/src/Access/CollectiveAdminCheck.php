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
    $user = isset($user) ? $user : User::load(\Drupal::currentUser()->id());
    $entity = \Drupal::routeMatch()->getParameter('node');
    $bundle = $entity ? $entity->bundle() : FALSE;

    if ($entity && in_array($bundle, ['art', 'performances', 'mutant_vehicles', 'theme_camps'])){
      $field_collective = $entity->get('field_collective');
      if ($field_collective) {
        $collective = $field_collective->first()->get('entity')->getTarget();
        return AccessResult::allowedIf($this->isAdmin($collective) || $user->hasRole('administrator'));
      }
      return AccessResult::allowedIf($user->hasRole('administrator'));
    }

    if ($bundle == 'collective') {
      return AccessResult::allowedIf($this->isAdmin($entity) || $user->hasRole('administrator'));
    }

    return AccessResult::allowedIf(TRUE);
  }

  /**
   * Checks whether the current user is an admin
   */
  private function isAdmin($collective){
    $uid = \Drupal::currentUser()->id();
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
