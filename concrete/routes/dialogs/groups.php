<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/group
 * Namespace: Concrete\Controller\Dialog\Group
 */

$router->all('/search', 'Search::view');
