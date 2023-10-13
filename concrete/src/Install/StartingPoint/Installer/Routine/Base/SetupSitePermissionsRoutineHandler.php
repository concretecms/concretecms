<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Application\Application;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\Entity\ConversationMessageAuthorEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\UserInfo;

class SetupSitePermissionsRoutineHandler
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke()
    {
        $g1 = Group::getByID(GUEST_GROUP_ID);
        $g2 = Group::getByID(REGISTERED_GROUP_ID);
        $g3 = Group::getByID(ADMIN_GROUP_ID);

        // login
        $login = Page::getByPath('/login', 'RECENT');
        $login->assignPermissions($g1, ['view_page']);

        // register
        $register = Page::getByPath('/register', 'RECENT');
        $register->assignPermissions($g1, ['view_page']);

        // Page Forbidden
        $page_forbidden = Page::getByPath('/page_forbidden', "RECENT");
        $page_forbidden->assignPermissions($g1, ['view_page']);

        // Page Not Found
        $page_not_found = Page::getByPath('/page_not_found', "RECENT");
        $page_not_found->assignPermissions($g1, ['view_page']);

        // drafts
        $drafts = Page::getByPath('/!drafts', 'RECENT');
        $drafts->assignPermissions(
            $g3,
            [
                'view_page',
                'view_page_versions',
                'view_page_in_sitemap',
                'preview_page_as_user',
                'edit_page_properties',
                'edit_page_contents',
                'edit_page_speed_settings',
                'edit_page_multilingual_settings',
                'edit_page_theme',
                'edit_page_template',
                'edit_page_page_type',
                'edit_page_permissions',
                'delete_page',
                'delete_page_versions',
                'approve_page_versions',
                'add_subpage',
                'move_or_copy_page',
                'schedule_page_contents_guest_access',
            ]
        );

        $home = Page::getByID(Page::getHomePageID(), 'RECENT');
        $home->assignPermissions($g1, ['view_page']);
        $home->assignPermissions(
            $g3,
            [
                'view_page_versions',
                'view_page_in_sitemap',
                'preview_page_as_user',
                'edit_page_properties',
                'edit_page_contents',
                'edit_page_speed_settings',
                'edit_page_multilingual_settings',
                'edit_page_theme',
                'edit_page_template',
                'edit_page_page_type',
                'edit_page_permissions',
                'delete_page',
                'delete_page_versions',
                'approve_page_versions',
                'add_subpage',
                'move_or_copy_page',
                'schedule_page_contents_guest_access',
            ]
        );

        $config = $this->app->make('config/database');
        $config->save('concrete.security.token.jobs', $this->app->make('helper/validation/identifier')->getString(64));
        $config->save('concrete.security.token.encryption', $this->app->make('helper/validation/identifier')->getString(64));
        $config->save('concrete.security.token.validation', $this->app->make('helper/validation/identifier')->getString(64));

        // group permissions
        $tree = GroupTree::get();
        $node = $tree->getRootTreeNodeObject();
        $permissions = [
            'search_group_folder',
            'edit_group_folder',
            'edit_group_folder_permissions',
            'delete_group_folder',
            'add_group',
            'assign_groups',
            'add_group_folder',
        ];
        $adminGroupEntity = GroupPermissionAccessEntity::getOrCreate($g3);
        foreach ($permissions as $pkHandle) {
            $pk = Key::getByHandle($pkHandle);
            $pk->setPermissionObject($node);
            $pa = PermissionAccess::create($pk);
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }


        // notification
        $adminUserEntity = UserEntity::getOrCreate(UserInfo::getByID(USER_SUPER_ID));
        $pk = Key::getByHandle('notify_in_notification_center');
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($adminUserEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        try {
            $this->app->make('helper/file')->makeExecutable(DIR_BASE_CORE . '/bin/concrete', 'all');
        } catch (\Exception $x) {
        }
    }


}
