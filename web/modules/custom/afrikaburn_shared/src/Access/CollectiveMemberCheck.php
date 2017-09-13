<?php

/**
 * @file
 * Contains Collective Member access checking.
 */

namespace Drupal\afrikaburn_shared\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Checks Collective Member access.
 */
class CollectiveMemberCheck implements AccessInterface {

  public function appliesTo() {
    return '_is_collective_member';
  }
  
  public function access(AccountInterface $account) {
    // $registration = \Drupal::routeMatch()->getParameter('node');
    // if (in_array($registration->bundle(), ['art', 'performances', 'mutant_vehicles', 'theme_camps']){

    //   $field_collective = $registration->get('field_collective');
    //   if ($field_collective) {
    //     $collective = $field_collective->first();
    //     $members = $collective
    //       ->get('entity')
    //       ->getTarget()
    //       ->get('field_col_members')
    //       ->referencedEntities();    
    //     foreach ($members as $member) {
    //       if ($member->id() == \Drupal::currentUser()->id()){
    //         return AccessResult::allowedIf(
    //           TRUE
    //         );          
    //       }
    //     }
    //   }

    //   return AccessResult::allowedIf(
    //     FALSE
    //   );

    // } else {
      return AccessResult::allowedIf(
        TRUE
      );
    // }

  }

}