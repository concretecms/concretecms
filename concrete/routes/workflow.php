<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/workflow
 * Namespace: Concrete\Controller\Workflow
 */

$router->all('/categories/page/save_progress', 'Categories\Page::saveProgress');
$router->all('/categories/user/save_progress', 'Categories\User::saveProgress');
$router->all('/categories/calendar_event/save_progress', 'Categories\CalendarEvent::saveProgress');
$router->all('/dialogs/approve_page_preview', 'Dialogs\ApprovePagePreview::view');
