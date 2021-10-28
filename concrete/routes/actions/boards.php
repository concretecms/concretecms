<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/board
 * Namespace: Concrete\Controller\Backend\Board
 */

$router->all('/instance/pin_slot', 'Instance::pinSlot');
$router->all('/instance/clear_slot', 'Instance::clearSlot');
$router->post('/instance/delete_rule', 'Instance::deleteRule');
$router->post('/instance/delete_rule_by_batch', 'Instance::deleteRuleByBatch');
