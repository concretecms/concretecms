<?php
namespace Concrete\Core\Permission\Inheritance\Registry;

use Concrete\Core\Permission\Inheritance\Registry\Entry\Entry;
use Concrete\Core\Permission\Registry\Entry\EntryInterface;

class BlockRegistry extends AbstractRegistry
{

    public function __construct()
    {
        $this->addEntry(new Entry('area', 'view_area', 'view_block'));
        $this->addEntry(new Entry('area', 'edit_area_contents', 'edit_block'));
        $this->addEntry(new Entry('area', 'edit_area_contents', 'edit_block_custom_template'));
        $this->addEntry(new Entry('area', 'edit_area_contents', 'edit_block_design'));
        $this->addEntry(new Entry('area', 'edit_area_permissions', 'edit_block_permissions'));
        $this->addEntry(new Entry('area', 'schedule_area_contents_guest_access', 'schedule_guest_access'));
        $this->addEntry(new Entry('area', 'edit_area_contents', 'edit_block_name'));
        $this->addEntry(new Entry('area', 'edit_area_contents', 'edit_block_cache_settings'));
        $this->addEntry(new Entry('area', 'delete_area_contents', 'delete_block'));

        $this->addEntry(new Entry('page', 'view_page', 'view_block'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'edit_block'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'edit_block_custom_template'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'edit_block_design'));
        $this->addEntry(new Entry('page', 'edit_page_permissions', 'edit_block_permissions'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'edit_block_name'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'edit_block_cache_settings'));
        $this->addEntry(new Entry('page', 'schedule_page_contents_guest_access', 'schedule_guest_access'));
        $this->addEntry(new Entry('page', 'edit_page_contents', 'delete_block'));
    }

}