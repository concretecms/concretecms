<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Block\Block;
use Area;
use Concrete\Core\Area\SubArea;
use Concrete\Core\Permission\Inheritance\Registry\RegistryInterface;
use PermissionKey;
use Page;
use Database;

class BlockAssignment extends Assignment
{
    protected $permissionObjectToCheck;

    /**
     * @param Block $b
     */
    public function setPermissionObject($b)
    {
        $this->permissionObject = $b;

        // if the area overrides the collection permissions explicitly (with a one on the override column) we check
        if ($b->overrideAreaPermissions()) {
            $this->permissionObjectToCheck = $b;
        } else {
            $a = $b->getBlockAreaObject();
            if ($a instanceof SubArea && !$a->overrideCollectionPermissions()) {
                $a = $a->getSubAreaParentPermissionsObject();
            }
            if (is_object($a)) {
                if ($a->overrideCollectionPermissions()) {
                    $this->permissionObjectToCheck = $a;
                } elseif ($a->getAreaCollectionInheritID()) {
                    $mcID = $a->getAreaCollectionInheritID();
                    $mc = Page::getByID($mcID, 'RECENT');
                    $ma = Area::get($mc, $a->getAreaHandle());
                    if ($ma->overrideCollectionPermissions()) {
                        $this->permissionObjectToCheck = $ma;
                    } else {
                        $this->permissionObjectToCheck = $ma->getAreaCollectionObject();
                    }
                } else {
                    $this->permissionObjectToCheck = $a->getAreaCollectionObject();
                }
            } else {
                $this->permissionObjectToCheck = Page::getCurrentPage();
            }
        }
    }

    public function getPermissionAccessObject()
    {
        /**
         * @var $registry RegistryInterface
         */
        $registry = \Core::make('Concrete\Core\Permission\Inheritance\Registry\BlockRegistry');
        $db = Database::connection();
        if ($this->permissionObjectToCheck instanceof Block) {
            $co = $this->permissionObjectToCheck->getBlockCollectionObject();
            $paID = $db->GetOne(
                'select paID from BlockPermissionAssignments where cID = ? and cvID = ? and bID = ? and pkID = ? ',
                array(
                    $co->getCollectionID(),
                    $co->getVersionID(),
                    $this->permissionObject->getBlockID(),
                    $this->pk->getPermissionKeyID(),
                )
            );
            if ($paID) {
                $pae = Access::getByID($paID, $this->pk, false);
            }
        } elseif ($this->permissionObjectToCheck instanceof Area && $registry->getEntry('area', $this->pk->getPermissionKeyHandle())) {
            $pk = PermissionKey::getByHandle($registry->getEntry('area', $this->pk->getPermissionKeyHandle())->getInheritedFromPermissionKeyHandle());
            $pk->setPermissionObject($this->permissionObjectToCheck);
            $pae = $pk->getPermissionAccessObject();
        } elseif ($this->permissionObjectToCheck instanceof Page && $registry->getEntry('page', $this->pk->getPermissionKeyHandle())) {
            $pk = PermissionKey::getByHandle($registry->getEntry('page', $this->pk->getPermissionKeyHandle())->getInheritedFromPermissionKeyHandle());
            $pk->setPermissionObject($this->permissionObjectToCheck);
            $pae = $pk->getPermissionAccessObject();
        }

        return $pae;
    }

    public function clearPermissionAssignment()
    {
        $db = Database::connection();
        $co = $this->permissionObject->getBlockCollectionObject();
        $db->Execute('update BlockPermissionAssignments set paID = 0 where pkID = ? and bID = ? and cvID = ? and cID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getBlockID(), $co->getVersionID(), $co->getCollectionID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Database::connection();
        $co = $this->permissionObject->getBlockCollectionObject();
        $arHandle = $this->permissionObject->getAreaHandle();
        $db->Replace(
            'BlockPermissionAssignments',
            array(
                'cID' => $co->getCollectionID(),
                'paID' => $pa->getPermissionAccessID(),
                'cvID' => $co->getVersionID(),
                'bID' => $this->permissionObject->getBlockID(),
                'pkID' => $this->pk->getPermissionKeyID(),
            ),
            array('cID', 'cvID', 'bID', 'pkID'),
            true
        );
        $pa->markAsInUse();
    }

    public function getPermissionKeyToolsURL($task = false)
    {
        $b = $this->getPermissionObject();
        $c = $b->getBlockCollectionObject();
        $arHandle = $b->getAreaHandle();

        return parent::getPermissionKeyToolsURL($task) . '&cID=' . $c->getCollectionID() . '&cvID=' . $c->getVersionID() . '&bID=' . $b->getBlockID() . '&arHandle=' . urlencode($arHandle);
    }
}
