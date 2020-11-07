<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/assets/localization
 * Namespace: Concrete\Controller\Frontend
 */

$router->get('/core/js', 'AssetsLocalization::getCoreJavascript');
$router->get('/select2/js', 'AssetsLocalization::getSelect2Javascript');
$router->get('/redactor/js', 'AssetsLocalization::getRedactorJavascript');
$router->get('/fancytree/js', 'AssetsLocalization::getFancytreeJavascript');
$router->get('/imageeditor/js', 'AssetsLocalization::getImageEditorJavascript');
$router->get('/jquery/ui/js', 'AssetsLocalization::getJQueryUIJavascript');
$router->get('/translator/js', 'AssetsLocalization::getTranslatorJavascript');
$router->get('/dropzone/js', 'AssetsLocalization::getDropzoneJavascript');
$router->get('/conversations/js', 'AssetsLocalization::getConversationsJavascript');
$router->get('/moment/js', 'AssetsLocalization::getMomentJavascript');
