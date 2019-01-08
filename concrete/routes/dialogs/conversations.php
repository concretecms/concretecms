<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/subscribe/{cnvID}', 'Subscribe::view');
$router->all('/subscribe/subscribe/{cnvID}', 'Subscribe::subscribe');
$router->all('/subscribe/unsubscribe/{cnvID}', 'Subscribe::unsubscribe');
