<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/design/', 'Design::view');
$router->all('/design/reset', 'Design::reset');
$router->all('/design/submit', 'Design::submit');
$router->all('/layout/presets/manage/', 'Layout\Presets\Manage::viewPresets');
$router->all('/layout/presets/manage/delete', 'Layout\Presets\Manage::delete');
$router->all('/layout/presets/{arLayoutID}', 'Layout\Presets::view');
$router->all('/layout/presets/{arLayoutID}/submit', 'Layout\Presets::submit');
$router->all('/layout/presets/get/{cID}/{arLayoutPresetID}', 'Layout\Presets::getPresetData');
