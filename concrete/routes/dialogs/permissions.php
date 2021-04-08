<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/permissions
 * Namespace: Concrete\Controller\Dialog\Permissions
 */

$router->all('/access/entity/site_group', 'Access\Entity\SiteGroup::view');
$router->all('/access/entity/types/group_combination', 'Access\Entity\Types\GroupCombination::view');
$router->all('/access/entity/types/group_set', 'Access\Entity\Types\GroupSet::view');
$router->all('/basic_workflow', 'BasicWorkflow::view');
$router->all('/block_type', 'BlockType::view');
$router->all('/conversation', 'Conversation::view');
$router->all('/file', 'File::view');
$router->all('/file_set', 'FileSet::view');
$router->all('/miscellaneous', 'Miscellaneous::view');
$router->all('/page_type', 'PageType::view');
$router->all('/tree/node', 'Tree\Node::view');
$router->all('/user', 'User::view');
$router->get('/collection', 'Collection::view');
