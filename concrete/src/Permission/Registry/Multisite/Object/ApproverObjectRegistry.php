<?php
namespace Concrete\Core\Permission\Registry\Multisite\Object;

use Concrete\Core\Permission\Registry\AbstractObjectRegistry;
use Concrete\Core\Permission\Registry\Entry\Object\Object\BasicWorkflow;
use Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder;
use Concrete\Core\Permission\Registry\Entry\Object\Object\HomePage;
use Concrete\Core\Permission\Registry\Entry\Object\Object\Page;
use Concrete\Core\Permission\Registry\Entry\Object\PermissionsEntry;
use Concrete\Core\Permission\Registry\Entry\Object\TaskPermissionsEntry;

class ApproverObjectRegistry extends AbstractObjectRegistry
{

    public function __construct()
    {
        $this->addEntry(new PermissionsEntry(new HomePage(), [
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
        ]));
        $this->addEntry(new PermissionsEntry(new FileFolder(''), [
            'search_file_folder',
        ]));
        $this->addEntry(new PermissionsEntry(new FileFolder('/' . t('Shared Files')), [
            'search_file_folder',
            'delete_file_folder_files',
            'add_file'
        ]));
        $this->addEntry(new PermissionsEntry(new BasicWorkflow('Content Approval'), [
            'approve_basic_workflow_action',
            'notify_on_basic_workflow_entry',
            'notify_on_basic_workflow_approve',
            'notify_on_basic_workflow_deny'
        ]));


        $this->addEntry(new PermissionsEntry(new Page('/dashboard/blocks/stacks'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/blocks'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/reports'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/users/search'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/files/search'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/users'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/files'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/sitemap'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/welcome'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard'), ['view_page'], false));

        $this->addEntry(new TaskPermissionsEntry('access_sitemap'));
        $this->addEntry(new TaskPermissionsEntry('add_block'));
        $this->addEntry(new TaskPermissionsEntry('add_stack'));
    }

}
