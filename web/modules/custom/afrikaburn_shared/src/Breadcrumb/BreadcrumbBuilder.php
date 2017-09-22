<?php

/**
 * @file
 * Contains Afrikaburn Breadcrumb Builder.
 */

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface {

    /**
     * @inheritdoc
     */
    public function applies(RouteMatchInterface $route_match) {
        /* Allways use this. Change this is another module needs to use a new custom breadcrumb */
        return true;
        /* This code allows for only the registration page to get used by this breadcrumb
         * $parameters = explode('.', $route_match->getRouteName());
         * if ($parameters[0] === 'registration') {
         *     return true;
         * } else {
         *     return false;
         * }
         */
    }

    /**
     * @inheritdoc
     */
    public function build(RouteMatchInterface $route_match) {
        $parameters = explode('.', $route_match->getRouteName());
        $b = new Breadcrumb();
        if ($parameters[0] === 'registration') {
            /* If registration page use these links */
            $b->setLinks($this->buildRegistration($parameters[1]));
        }
        return $b;
    }

    /**
     * Creates all the links for the registration breadcrumb
     * @param type $page
     * @return type
     */
    private function buildRegistration($page) {
        return [
            Link::createFromRoute(t('Step One'), 'registration.one'),
            Link::createFromRoute(t('Step Two'), 'registration.two'),
            Link::createFromRoute(t('Step Three'), 'registration.three'),
            Link::createFromRoute(t('Step Four'), 'registration.four'),
            Link::createFromRoute(t('Step Five'), 'registration.five'),
            Link::createFromRoute(t('Step Six'), 'registration.six'),
            Link::createFromRoute(t('Step Seven'), 'registration.seven')
        ];
    }

}