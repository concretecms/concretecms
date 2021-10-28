<?php

namespace Concrete\Core\Permission\Registry\Multisite\Object;

use Concrete\Core\Permission\Registry\AbstractObjectRegistry;
use Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder;
use Concrete\Core\Permission\Registry\Entry\Object\Object\HomePage;
use Concrete\Core\Permission\Registry\Entry\Object\Object\Page;
use Concrete\Core\Permission\Registry\Entry\Object\PermissionsEntry;
use Concrete\Core\Permission\Registry\Entry\Object\TaskPermissionsEntry;

class AuthorObjectRegistry extends AbstractObjectRegistry
{
    public function __construct()
    {
        $this->addEntry(new PermissionsEntry(new HomePage(), [
            'view_page_versions',
            'view_page_in_sitemap',
            'edit_page_properties',
            'edit_page_contents',
            'edit_page_template',
            'edit_page_page_type',
            'delete_page',
            'delete_page_versions',
            'add_subpage',
        ]));
        $this->addEntry(new PermissionsEntry(new FileFolder(''), [
            'search_file_folder',
        ]));
        $this->addEntry(new PermissionsEntry(new FileFolder('/' . t('Shared Files')), [
            'search_file_folder',
            'add_file',
        ]));

        $this->addEntry(new PermissionsEntry(new Page('/dashboard/blocks/stacks'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/blocks'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/files/search'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/files'), ['view_page'], false));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/sitemap'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard/welcome'), ['view_page']));
        $this->addEntry(new PermissionsEntry(new Page('/dashboard'), ['view_page'], false));

        $this->addEntry(new TaskPermissionsEntry('access_sitemap'));
        $this->addEntry(new TaskPermissionsEntry('add_block'));
        $this->addEntry(new TaskPermissionsEntry('add_stack'));
    }
}
