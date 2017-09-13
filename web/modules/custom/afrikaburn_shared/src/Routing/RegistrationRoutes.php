<?php

/**
 * @file
 * Contains Afrikaburn Shared module.
 */

namespace Drupal\afrikaburn_shared\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RegistrationRoutes extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    // Registration views
    if ($route = $collection->get('entity.node.canonical')) {
      $route->setRequirement('_is_collective_member', 'TRUE');
    }

    // Registration Edits
    if ($route = $collection->get('entity.node.edit_form')) {
      $route->setRequirement('_is_collective_admin', 'TRUE');
    }

  }

}