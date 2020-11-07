<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$router->get('/rss/{identifier}', 'Concrete\Controller\Feed::output')
    ->setName('rss')
;
$router->get('/ccm/calendar/feed/{calendar_id}', 'Concrete\Controller\CalendarFeed::view')
    ->setName('calendar_rss')
    ->setRequirements(['calendar_id' => '[0-9]+'])
;
