<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router $router
 */
/*
 * Base path: /ccm/system/dialogs/type
 * Namespace: Concrete\Controller\Dialog\Type\
 */
$router->all('/update_from_type/{ptID}/{pTemplateID}', 'UpdateFromType::view');
$router->all('/update_from_type/{ptID}/{pTemplateID}/submit', 'UpdateFromType::submit');

$router->all('/attributes/{ptID}', 'Attributes::view');
$router->all('/attributes/{ptID}/get_attribute', 'Attributes::getAttribute');
$router->all('/attributes/{ptID}/submit', 'Attributes::submit');
