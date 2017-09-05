<?php
/**
 * @file
 * Contains \Drupal\hello\PostMigrationController.
 */

namespace Drupal\hello;


use Drupal\Core\Controller\ControllerBase;


class PostMigrationController extends ControllerBase {
  public function rebuildUsers() {

    set_time_limit(0);
    $uids = db_query('SELECT uid FROM {users} WHERE uid != 0')->fetchCol();
    $users = user_load_multiple($uids);
    foreach ($users as $user) {
      user_save($user);
    }

    return array(
      '#markup' => count($users) . ' users rebuilt',
    );
  }
}