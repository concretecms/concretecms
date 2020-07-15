<?php

namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Key\AreaKey;
use Concrete\Core\Permission\Key\PageKey;
use Exception;

class StackAssignment extends PageAssignment
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::setPermissionKeyObject()
     */
    public function setPermissionKeyObject($pk)
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

        if ($pk instanceof AreaKey) {
            $areaKeyHandle = $pk->getPermissionKeyHandle();
            if (isset($map[$areaKeyHandle])) {
                $pageKeyHandle = $map[$areaKeyHandle];
            }
            $pk2 = isset($pageKeyHandle) ? PageKey::getByHandle($pageKeyHandle) : null;
            if ($pk2 === null) {
                throw new Exception(t('Unsupported area key: %s', $areaKeyHandle));
            }
            $pk2->setPermissionObject($this->getPermissionObject());
            $pk = $pk2;
        }
        $this->pk = $pk;
    }
}
