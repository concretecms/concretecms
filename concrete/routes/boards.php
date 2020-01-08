<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 */
$router->all('/ccm/system/dialogs/boards/permissions/{pkCategoryHandle}', '\Concrete\Controller\Dialog\Board\Permissions::view');

/* Permissions Tools Hack */
$router->all('/tools/required/permissions/categories/board_admin', '\Concrete\Controller\Board\Permissions::process');
$router->all('/tools/required/permissions/categories/board', '\Concrete\Controller\Event\Permissions::processCalendar');
