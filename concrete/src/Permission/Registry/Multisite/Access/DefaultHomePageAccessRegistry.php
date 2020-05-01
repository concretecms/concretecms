<?php
namespace Concrete\Core\Permission\Registry\Multisite\Access;

use Concrete\Core\Permission\Registry\AbstractAccessRegistry;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Registry\Entry\Access\PermissionsEntry;

class DefaultHomePageAccessRegistry extends AbstractAccessRegistry
{

    protected $guestPermissions = [
        'view_page',
    ];

    protected $administratorPermissions = [
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
        'schedule_page_contents_guest_access'
    ];

    protected $editorPermissions = [
        'view_page_versions',
        'view_page_in_sitemap',
        'edit_page_properties',
        'edit_page_contents',
        'edit_page_template',
        'edit_page_page_type',
        'delete_page',
        'delete_page_versions',
        'approve_page_versions',
        'add_subpage',
    ];

    public function __construct()
    {
        $this->addEntry(new PermissionsEntry(new GroupEntity('Guest'), $this->guestPermissions));
        $this->addEntry(new PermissionsEntry(new GroupEntity('Administrators'), $this->administratorPermissions));
    }


}
