<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$router->all('/ccm/system/tree/load', '\Concrete\Controller\Backend\Tree::load');
$router->all('/ccm/system/tree/node/load', '\Concrete\Controller\Backend\Tree\Node::load');
$router->all('/ccm/system/tree/node/load_starting', '\Concrete\Controller\Backend\Tree\Node::load_starting');
$router->all('/ccm/system/tree/node/drag_request', '\Concrete\Controller\Backend\Tree\Node\DragRequest::execute');
$router->all('/ccm/system/tree/node/duplicate', '\Concrete\Controller\Backend\Tree\Node\Duplicate::execute');
$router->all('/ccm/system/tree/node/update_order', '\Concrete\Controller\Backend\Tree\Node\DragRequest::updateChildren');

$router->all('/ccm/system/dialogs/tree/node/add/category', '\Concrete\Controller\Dialog\Tree\Node\Category\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/category/add_category_node', '\Concrete\Controller\Dialog\Tree\Node\Category\Add::add_category_node');

$router->all('/ccm/system/dialogs/tree/node/add/topic', '\Concrete\Controller\Dialog\Tree\Node\Topic\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/topic/add_topic_node', '\Concrete\Controller\Dialog\Tree\Node\Topic\Add::add_topic_node');

$router->all('/ccm/system/dialogs/tree/node/edit/topic', '\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/topic/update_topic_node', '\Concrete\Controller\Dialog\Tree\Node\Topic\Edit::update_topic_node');

$router->all('/ccm/system/dialogs/tree/node/edit/category', '\Concrete\Controller\Dialog\Tree\Node\Category\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/category/update_category_node', '\Concrete\Controller\Dialog\Tree\Node\Category\Edit::update_category_node');

$router->all('/ccm/system/dialogs/tree/node/add/file_folder', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/file_folder/add_file_folder_node', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Add::add_file_folder_node');
$router->all('/ccm/system/dialogs/tree/node/edit/file_folder', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/file_folder/update_file_folder_node', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Edit::update_file_folder_node');
$router->get('/ccm/system/dialogs/tree/node/delete/file_folder', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Delete::view');
$router->post('/ccm/system/dialogs/tree/node/delete/file_folder/remove_tree_node', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Delete::remove_tree_node');

$router->all('/ccm/system/dialogs/tree/node/move/file_folder', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Move::view');
$router->all('/ccm/system/dialogs/tree/node/move/file_folder/submit', '\Concrete\Controller\Dialog\Tree\Node\FileFolder\Move::submit');

$router->all('/ccm/system/dialogs/tree/node/add/group_folder', '\Concrete\Controller\Dialog\Tree\Node\GroupFolder\Add::view');
$router->all('/ccm/system/dialogs/tree/node/add/group_folder/add_group_folder_node', '\Concrete\Controller\Dialog\Tree\Node\GroupFolder\Add::add_group_folder_node');
$router->all('/ccm/system/dialogs/tree/node/edit/group_folder', '\Concrete\Controller\Dialog\Tree\Node\GroupFolder\Edit::view');
$router->all('/ccm/system/dialogs/tree/node/edit/group_folder/update_group_folder_node', '\Concrete\Controller\Dialog\Tree\Node\GroupFolder\Edit::update_group_folder_node');

$router->all('/ccm/system/dialogs/tree/node/delete', '\Concrete\Controller\Dialog\Tree\Node\Delete::view');
$router->all('/ccm/system/dialogs/tree/node/delete/remove_tree_node', '\Concrete\Controller\Dialog\Tree\Node\Delete::remove_tree_node');
$router->all('/ccm/system/dialogs/tree/node/permissions', '\Concrete\Controller\Dialog\Tree\Node\Permissions::view');
$router->all('/ccm/system/dialogs/tree/node/category/delete_express', '\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::view');
$router->all('/ccm/system/dialogs/tree/node/category/delete_express/remove_tree_node', '\Concrete\Controller\Dialog\Tree\Node\Category\DeleteExpress::remove_tree_node');
