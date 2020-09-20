<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/page/type
 * Namespace: Concrete\Controller\Backend\Page\Type
 */

$router->all('composer/form/add_control', 'Composer\Form\AddControl::view');
$router->post('composer/form/add_control/pick', 'Composer\Form\AddControl\Pick::view');
