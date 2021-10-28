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

$router->get(
    '/ccm/system/permissions/access/entity/types/user/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\User::getOrCreate'
);
$router->get(
    '/ccm/system/permissions/access/entity/types/page_owner/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\PageOwner::getOrCreate'
);
$router->get(
    '/ccm/system/permissions/access/entity/types/group/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\Group::getOrCreate'
);
$router->get(
    '/ccm/system/permissions/access/entity/types/group_set/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\GroupSet::getOrCreate'
);
$router->get(
    '/ccm/system/permissions/access/entity/types/file_uploader/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\FileUploader::getOrCreate'
);
$router->get(
    '/ccm/system/permissions/access/entity/types/conversation_message_author/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\ConversationMessageAuthor::getOrCreate'
);
$router->post(
    '/ccm/system/permissions/access/entity/types/group_combination/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\GroupCombination::getOrCreate'
);
$router->post(
    '/ccm/system/permissions/access/entity/types/site_group/get_or_create',
    'Concrete\Controller\Permissions\Access\Entity\SiteGroup::getOrCreate'
);
$router->all(
    '/ccm/system/permissions/access/entity',
    'Concrete\Controller\Permissions\Access\Entity::view'
);
$router->post(
    '/ccm/system/permissions/access/entity/save',
    'Concrete\Controller\Permissions\Access\Entity::save'
);
$router->post(
    '/ccm/system/permissions/set/copy',
    'Concrete\Controller\Permissions\Set\Clipboard::copy'
);
$router->post(
    '/ccm/system/permissions/set/paste',
    'Concrete\Controller\Permissions\Set\Clipboard::paste'
);
$router
    ->all(
        '/ccm/system/permissions/categories/{categoryHandle}/{task}',
        'Concrete\Core\Permission\Category\TaskLauncher::launch'
    )
    ->setRequirements(['categoryHandle' => '\w[\w\-]*'])
;
