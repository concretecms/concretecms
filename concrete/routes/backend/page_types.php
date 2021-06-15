<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/page/type
 * Namespace: Concrete\Controller\Backend\Page\Type
 */

$router->all('composer/form/add_control', 'Composer\Form\AddControl::view');
$router->post('composer/form/add_control/pick', 'Composer\Form\AddControl\Pick::view');

$router->all('composer/form/edit_control', 'Composer\Form\EditControl::view');
$router->post('composer/form/edit_control/save', 'Composer\Form\EditControl\Save::view');
