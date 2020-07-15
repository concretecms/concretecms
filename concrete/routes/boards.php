<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 */
$router->all('/ccm/system/dialogs/boards/permissions/{pkCategoryHandle}', '\Concrete\Controller\Dialog\Board\Permissions::view');
$router->all('/ccm/system/dialogs/boards/custom_slot/replace', '\Concrete\Controller\Dialog\Board\CustomSlot::replace');
$router->post('/ccm/system/dialogs/boards/custom_slot/search_items', '\Concrete\Controller\Dialog\Board\CustomSlot::searchItems');
$router->all('/ccm/system/dialogs/boards/custom_slot/get_templates', '\Concrete\Controller\Dialog\Board\CustomSlot::getTemplates');
$router->all('/ccm/system/dialogs/boards/custom_slot/save_template', '\Concrete\Controller\Dialog\Board\CustomSlot::saveTemplate');
$router->all('/ccm/system/board/instance/preview_rule/{boardInstanceSlotRuleID}', '\Concrete\Controller\Backend\Board\Instance\PreviewRule::view');
$router->all('/ccm/system/board/element/preview/{elementID}', '\Concrete\Controller\Backend\Board\Element\Preview::view');

/* Permissions Tools Hack */
$router->all('/tools/required/permissions/categories/board_admin', '\Concrete\Controller\Board\Permissions::process');
$router->all('/tools/required/permissions/categories/board', '\Concrete\Controller\Event\Permissions::processCalendar');
