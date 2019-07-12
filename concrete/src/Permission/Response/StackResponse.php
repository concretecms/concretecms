<?php

namespace Concrete\Core\Permission\Response;

use Concrete\Core\Block\Block;
use Concrete\Core\Permission\Category;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

/**
 * Stacks and global area permissions are actually "page" permissions.
 * So, we need to translate the "area" permission keys to the "page" permission keys.
 */
class StackResponse extends PageResponse
{
    public function canAddBlocks()
    {
        return $this->validate('edit_page_contents');
    }

    public function canAddStacks()
    {
        return $this->validate('edit_page_contents');
    }

    public function canAddStack()
    {
        return $this->validate('edit_page_contents');
    }

    public function canAddLayout()
    {
        return $this->validate('edit_page_contents');
    }

    /**
     * @param \Concrete\Core\Block\Block|\Concrete\Core\Block\BlockType\BlockType $blockTypeOrBlock
     *
     * @return bool
     */
    public function canAddBlock($blockTypeOrBlock)
    {
        if ($blockTypeOrBlock instanceof Block) {
            $blockType = $blockTypeOrBlock->getBlockTypeObject();
        } else {
            $blockType = $blockTypeOrBlock;
        }
        switch ($blockType->getBlockTypeHandle()) {
            case BLOCK_HANDLE_LAYOUT_PROXY:
                return $this->canAddLayout();
            case BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY:
                return $this->canAddBlocks();
        }
        $pkc = Category::getByHandle('area');
        $pk = $pkc->getPermissionKeyByHandle('add_block_to_area');
        $pk->setPermissionObject($this->object->getArea(STACKS_AREA_NAME));

        return $pk->validate($blockTypeOrBlock);
    }

    public function canViewAreaControls()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);

        return
            $u->isSuperUser() ||
            $this->canEditPageContents() ||
            $this->canEditPagePermissions()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Response\Response::validate()
     */
    public function validate($permissionHandle, $args = [])
    {
        static $map = [
            'add_block_to_area' => 'edit_page_contents',
            'add_layout_to_area' => 'edit_page_contents',
            'add_stack_to_area' => 'edit_page_contents',
            'delete_area_contents' => 'edit_page_contents',
            'edit_area_contents' => 'edit_page_contents',
            'edit_area_design' => 'edit_page_properties',
            'edit_area_permissions' => 'edit_page_permissions',
            'schedule_area_contents_guest_access' => 'schedule_page_contents_guest_access',
            'view_area' => 'view_page',
        ];

        $pagePermissionHandle = isset($map[$permissionHandle]) ? $map[$permissionHandle] : $permissionHandle;

        return parent::validate($pagePermissionHandle, $args);
    }
}
