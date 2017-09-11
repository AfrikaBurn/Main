<?php

/**
 * @file
 * Contains \Drupal\afrikaburn_agreement\EventSubscriber\AgreementEnforcer.
 */

namespace Drupal\afrikaburn_agreement\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AgreementEnforcer implements EventSubscriberInterface {

  public function checkForRedirection(GetResponseEvent $event) {

    if (preg_match('^\/user\/reset', \Drupal::service('path.current')->getPath())) return;

    $uid = \Drupal::currentUser()->id();
    $user = \Drupal::entityTypeManager()->getStorage('user')->load($uid);

    if (count($user->field_agreements)) {
      foreach($user->field_agreements->referencedEntities() as $agreement){

        $aid = $agreement->id();

        foreach($agreement->field_agreement_terms->referencedEntities() as $webform){

          $done = db_select('webform_submission')
            ->condition('uid', $uid)
            ->condition('entity_id', $aid)
            ->condition('webform_id', $webform->id())
            ->countQuery()
              ->execute()
              ->fetchField();

          $node = \Drupal::routeMatch()->getParameter('node');
          $nid = $node ? $node->id() : FALSE;

          if (!$done && $nid != $aid) {
            $event->setResponse(new RedirectResponse('/node/' . $aid));
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('checkForRedirection');
    return $events;
  }

}