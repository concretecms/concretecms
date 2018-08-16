<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\API\OAuth\Controller as OAuthController;

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

$router->post('/oauth/2.0/token', [OAuthController::class, 'token']);
