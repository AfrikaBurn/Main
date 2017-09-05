<?php
/**
 * @file
 * Contains \Drupal\afrikaburn_migration\AfrikaburnUserRebuilder.
 */

namespace Drupal\afrikaburn_migration;


use Drupal\Core\Controller\ControllerBase;


class AfrikaburnUserRebuilder extends ControllerBase {
  public function rebuildUsers() {

    set_time_limit(0);
    $uids = db_query('SELECT uid FROM {users} WHERE uid != 0')->fetchCol();
    $users = user_load_multiple($uids);
    foreach ($users as $user) {
      $user->save();
    }

    return array(
      '#markup' => count($users) . ' users rebuilt',
    );
  }
}