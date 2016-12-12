<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Area\Area;
use Stack;
use Page;
use Concrete\Core\Area\SubArea;
use PermissionKey;
use Database;

class AreaAssignment extends Assignment
{
    protected $area;
    protected $permissionObjectToCheck;
    protected $inheritedPermissions = array(
        'view_area' => 'view_page',
        'edit_area_contents' => 'edit_page_contents',
        'add_layout_to_area' => 'edit_page_contents',
        'edit_area_design' => 'edit_page_contents',
        'edit_area_permissions' => 'edit_page_permissions',
        'schedule_area_contents_guest_access' => 'schedule_page_contents_guest_access',
        'delete_area_contents' => 'edit_page_contents',
    );

    protected $blockTypeInheritedPermissions = array(
        'add_block_to_area' => 'add_block',
        'add_stack_to_area' => 'add_stack',
    );

    /**
     * @param Area $a
     */
    public function setPermissionObject($a)
    {
        $ax = $a;
        if ($a->isGlobalArea()) {
            $cx = Stack::getByName($a->getAreaHandle(), 'ACTIVE');
            $a = Area::get($cx, STACKS_AREA_NAME);
            if (!is_object($a)) {
                return false;
            }
        }

        if ($a instanceof SubArea && !$a->overrideCollectionPermissions()) {
            $a = $a->getSubAreaParentPermissionsObject();
        }
        $this->permissionObject = $a;

        // if the area overrides the collection permissions explicitly (with a one on the override column) we check
        if ($a->overrideCollectionPermissions()) {
            $this->permissionObjectToCheck = $a;
        } else {
            if ($a->getAreaCollectionInheritID() > 0) {
                // in theory we're supposed to be inheriting some permissions from an area with the same handle,
                // set on the collection id specified above (inheritid). however, if someone's come along and
                // reverted that area to the page's permissions, there won't be any permissions, and we
                // won't see anything. so we have to check
                $areac = Page::getByID($a->getAreaCollectionInheritID());
                $inheritArea = Area::get($areac, $a->getAreaHandle());
                if (is_object($inheritArea) && $inheritArea->overrideCollectionPermissions()) {
                    // okay, so that area is still around, still has set permissions on it. So we
                    // pass our current area to our grouplist, userinfolist objects, knowing that they will
                    // smartly inherit the correct items.
                    $this->permissionObjectToCheck = $inheritArea;
                }
            }

            if (!$this->permissionObjectToCheck) {
                $this->permissionObjectToCheck = $a->getAreaCollectionObject();
            }
        }
    }

    public function getPermissionAccessObject()
    {
        $db = Database::connection();

        if ($this->permissionObjectToCheck instanceof Area) {
            $r = $db->GetOne(
                'select paID from AreaPermissionAssignments where cID = ? and arHandle = ? and pkID = ? ',
                array(
                    $this->permissionObjectToCheck->getCollectionID(),
                    $this->permissionObjectToCheck->getAreaHandle(),
                    $this->pk->getPermissionKeyID(),
                )
            );
            if ($r) {
                return Access::getByID($r, $this->pk, false);
            }
        } elseif (isset($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            // this is a page
            $pk = PermissionKey::getByHandle($this->inheritedPermissions[$this->pk->getPermissionKeyHandle()]);
            $pk->setPermissionObject($this->permissionObjectToCheck);
            $pae = $pk->getPermissionAccessObject();

            return $pae;
        } elseif (isset($this->blockTypeInheritedPermissions[$this->pk->getPermissionKeyHandle()])) {
            $pk = PermissionKey::getByHandle($this->blockTypeInheritedPermissions[$this->pk->getPermissionKeyHandle()]);
            $pae = $pk->getPermissionAccessObject();

            return $pae;
        }

        return false;
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        $area = $this->getPermissionObject();
        $c = $area->getAreaCollectionObject();

        return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&arHandle=' . urlencode($area->getAreaHandle());
    }

    public function clearPermissionAssignment()
    {
        $db = Database::connection();
        $area = $this->getPermissionObject();
        $c = $area->getAreaCollectionObject();
        $db->Execute('update AreaPermissionAssignments set paID = 0 where pkID = ? and cID = ? and arHandle = ?', array($this->pk->getPermissionKeyID(), $c->getCollectionID(), $area->getAreaHandle()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Database::connection();
        $db->Replace(
            'AreaPermissionAssignments',
            array(
                'cID' => $this->getPermissionObject()->getCollectionID(),
                'arHandle' => $this->getPermissionObject()->getAreaHandle(),
                'paID' => $pa->getPermissionAccessID(),
                'pkID' => $this->pk->getPermissionKeyID(),
            ),
            array('cID', 'arHandle', 'pkID'),
            true
        );
        $pa->markAsInUse();
    }
}
