<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/action/{action}', 'Attribute\Action::dispatch')
    ->setName('attribute_action')
    ->setRequirements(['action' => '.+']);
$router->all('/attribute_sort/set', 'Attributes::sortInSet');
$router->all('/attribute_sort/user', 'Attributes::sortUser');
