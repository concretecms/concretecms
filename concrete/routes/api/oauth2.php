<?php

use Concrete\Core\Api\OAuth\Controller as OAuthController;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$router->post('/oauth/2.0/token', [OAuthController::class, 'token']);
$router->all('/oauth/2.0/authorize', [OAuthController::class, 'authorize']);
