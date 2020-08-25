<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router $router
 */
/*
 * Base path: /ccm/system/backend/dashboard
 * Namespace: Concrete\Controller\Backend\Dashboard
 */
$router
    ->all('get_image_data', 'GetImageData::view')
;
