<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 */
$router->get('/rss/{identifier}', 'Concrete\Controller\Feed::output')
    ->setName('rss');
$router->get('/ccm/calendar/feed/{calendar_id}', 'Concrete\Controller\CalendarFeed::view')
    ->setName('calendar_rss')
    ->setRequirements(['calendar_id' => '[0-9]+']);

