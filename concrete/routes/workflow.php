<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/workflow
 * Namespace: Concrete\Controller\Workflow\
 */

$router->all('/categories/page/save_progress', 'Categories\Page::saveProgress');
