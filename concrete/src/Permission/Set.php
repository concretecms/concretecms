<?php
namespace Concrete\Core\Permission;

use Session;

class Set
{
    protected $permissions;
    protected $pkCategoryHandle;

    public function addPermissionAssignment($pkID, $paID)
    {
        $this->permissions[$pkID] = $paID;
    }

    public function getPermissionAssignments()
    {
        return $this->permissions;
    }

    public function setPermissionKeyCategory($pkCategoryHandle)
    {
        $this->pkCategoryHandle = $pkCategoryHandle;
    }

    public function getPermissionKeyCategory()
    {
        return $this->pkCategoryHandle;
    }

    public function saveToSession()
    {
        Session::set('savedPermissionSet', serialize($this));
    }

    public static function getSavedPermissionSetFromSession()
    {
        // Restrict to Set::class to prevent PHP Object Injection via the session
        // backend (Redis, DB, file) which may be influenced by an attacker.
        $obj = unserialize(Session::get('savedPermissionSet'), ['allowed_classes' => [self::class]]);

        return $obj;
    }
}
