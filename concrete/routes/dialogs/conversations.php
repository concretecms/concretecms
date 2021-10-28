<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/conversation
 * Namespace: Concrete\Controller\Dialog\Conversation\
 */

$router->all('/subscribe/{cnvID}', 'Subscribe::view');
$router->all('/subscribe/subscribe/{cnvID}', 'Subscribe::subscribe');
$router->all('/subscribe/unsubscribe/{cnvID}', 'Subscribe::unsubscribe');
