<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/area
 * Namespace: Concrete\Controller\Dialog\Area
 */

$router->all('/design/', 'Design::view');
$router->all('/design/reset', 'Design::reset');
$router->all('/design/submit', 'Design::submit');
$router->all('/layout/presets/manage/', 'Layout\Presets\Manage::viewPresets');
$router->all('/layout/presets/manage/delete', 'Layout\Presets\Manage::delete');
$router->all('/layout/presets/{arLayoutID}', 'Layout\Presets::view');
$router->all('/layout/presets/{arLayoutID}/submit', 'Layout\Presets::submit');
$router->all('/layout/presets/get/{cID}/{arLayoutPresetID}', 'Layout\Presets::getPresetData');
$router->all('/edit/permissions', 'Edit\Permissions::view');
$router->all('/edit/advanced_permissions', 'Edit\AdvancedPermissions::view');
