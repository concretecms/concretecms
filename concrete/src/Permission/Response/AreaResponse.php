<?php

namespace Concrete\Core\Permission\Response;

use Concrete\Core\Block\Block;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

class AreaResponse extends Response
{
    // legacy support
    public function canRead()
    {
        return $this->validate('view_area');
    }

    public function canWrite()
    {
        return $this->validate('edit_area_contents');
    }

    public function canAdmin()
    {
        return $this->validate('edit_area_permissions');
    }

    public function canAddBlocks()
    {
        return $this->validate('add_block_to_area');
    }

    public function canAddStacks()
    {
        return $this->validate('add_stack_to_area');
    }

    public function canAddStack()
    {
        return $this->validate('add_stack_to_area');
    }

    public function canAddLayout()
    {
        return $this->validate('add_layout_to_area');
    }

    /**
     * Check if a new block can be added to the area, or if an existing block can be moved to it.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|\Concrete\Core\Block\Block $blockTypeOrBlock specify a block type when adding a new block, a block instance when adding an existing block
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
        $pk = $this->category->getPermissionKeyByHandle('add_block_to_area');
        $pk->setPermissionObject($this->object);

        return $pk->validate($blockTypeOrBlock);
    }

    // convenience function
    public function canViewAreaControls()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        if ($u->isSuperUser()) {
            return true;
        }

        if (
        $this->canEditAreaContents() ||
        $this->canEditAreaPermissions() ||
        $this->canAddBlockToArea() ||
        $this->canAddStackToArea() ||
        $this->canAddLayoutToArea()) {
            return true;
        }

        return false;
    }
}
