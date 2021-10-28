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

$router->all('/ccm/calendar/dialogs/event/edit', '\Concrete\Controller\Dialog\Event\Edit::edit');
$router->all('/ccm/calendar/dialogs/event/add', '\Concrete\Controller\Dialog\Event\Edit::add');
$router->all('/ccm/calendar/dialogs/event/add/save', '\Concrete\Controller\Dialog\Event\Edit::addEvent');
$router->all('/ccm/calendar/dialogs/event/edit/save', '\Concrete\Controller\Dialog\Event\Edit::updateEvent');
$router->all('/ccm/calendar/dialogs/event/duplicate', '\Concrete\Controller\Dialog\Event\Duplicate::view');
$router->all('/ccm/calendar/dialogs/event/duplicate/submit', '\Concrete\Controller\Dialog\Event\Duplicate::submit');
$router->all('/ccm/calendar/dialogs/event/delete', '\Concrete\Controller\Dialog\Event\Delete::view');
$router->all('/ccm/calendar/dialogs/event/delete_occurrence', '\Concrete\Controller\Dialog\Event\DeleteOccurrence::view');
$router->all('/ccm/calendar/dialogs/event/delete/submit', '\Concrete\Controller\Dialog\Event\Delete::submit');
$router->all('/ccm/calendar/dialogs/event/delete_occurrence/submit', '\Concrete\Controller\Dialog\Event\DeleteOccurrence::submit');
$router->all('/ccm/calendar/dialogs/event/versions', '\Concrete\Controller\Dialog\Event\Versions::view');
$router->all('/ccm/calendar/dialogs/event/version/view', '\Concrete\Controller\Dialog\Event\ViewVersion::view');
$router->all('/ccm/calendar/event/version/delete', '\Concrete\Controller\Event\EventVersion::delete');
$router->all('/ccm/calendar/event/version/approve', '\Concrete\Controller\Event\EventVersion::approve');
$router->all('/ccm/calendar/event/version/unapprove_all', '\Concrete\Controller\Event\Event::unapprove');
$router->get('/ccm/calendar/view_event/{bID}/{occurrence_id}', '\Concrete\Controller\Dialog\Frontend\Event::view')
    ->setName('view_event_occurrence')
    ->setRequirements(['occurrence_id' => '[0-9]+'])
;
$router->all('/ccm/calendar/dialogs/event/occurrence', '\Concrete\Controller\Dialog\EventOccurrence::view');
$router->all('/ccm/calendar/dialogs/choose_event', '\Concrete\Controller\Dialog\ChooseEvent::view');
$router->all('/ccm/calendar/dialogs/choose_event/get_events', '\Concrete\Controller\Dialog\ChooseEvent::getEvents');
$router->all('/ccm/calendar/event/get_json', '\Concrete\Controller\Event\Event::getJSON');
$router->all('/ccm/calendar/event/export', '\Concrete\Controller\Event\Export::export');
$router->post('/ccm/calendar/event_occurrence/get_json', '\Concrete\Controller\Event\Event::getOccurrence');
$router->all('/ccm/calendar/dialogs/permissions/{pkCategoryHandle}', '\Concrete\Controller\Dialog\Calendar\Permissions::view');

$router->all('/ccm/calendar/dialogs/event/summary_templates', '\Concrete\Controller\Dialog\Event\SummaryTemplates::view');
$router->all('/ccm/calendar/dialogs/event/summary_templates/submit', '\Concrete\Controller\Dialog\Event\SummaryTemplates::submit');

//$router->all('/ccm/calendar/dialogs/event/advanced_search', 'AdvancedSearch::view');
$router->all('/ccm/calendar/dialogs/event/advanced_search/add_field', '\Concrete\Controller\Dialog\Event\AdvancedSearch::addField');
//$router->all('/ccm/calendar/dialogs/event/advanced_search/submit', 'AdvancedSearch::submit');
//$router->all('/ccm/calendar/dialogs/event/advanced_search/save_preset', 'AdvancedSearch::savePreset');

//$router->all('/ccm/calendar/dialogs/event/advanced_search/preset/edit', 'Preset\Edit::view');
//$router->all('/ccm/calendar/dialogs/event/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
//$router->all('/ccm/calendar/dialogs/event/advanced_search/preset/delete', 'Preset\Delete::view');
//$router->all('/ccm/calendar/dialogs/event/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');
