<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/update_from_type/{ptID}/{pTemplateID}', 'UpdateFromType::view');
$router->all('/update_from_type/{ptID}/{pTemplateID}/submit', 'UpdateFromType::submit');

