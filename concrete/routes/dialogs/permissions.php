<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 * Base path: /ccm/system/dialogs/permissions
 * Namespace: Concrete\Controller\Dialog\Permissions\
 */
$router->all('/access/entity/site_group/{pkCategoryHandle}/{permissionObjectId}', 'Access\Entity\SiteGroup::view');
