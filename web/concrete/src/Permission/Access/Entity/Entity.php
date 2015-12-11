<?php

namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Foundation\Object;
use Database;
use PermissionAccess;
use CacheLocal;
use Core;
use RuntimeException;

abstract class Entity extends Object
{
    public function getAccessEntityTypeID()
    {
        return $this->petID;
    }

    public function getAccessEntityTypeObject()
    {
        return Type::getByID($this->petID);
    }

    public function getAccessEntityTypeHandle()
    {
        return $this->petHandle;
    }

    public function getAccessEntityID()
    {
        return $this->peID;
    }

    public function getAccessEntityLabel()
    {
        return $this->label;
    }

    abstract public function getAccessEntityUsers(PermissionAccess $pa);

    abstract public function getAccessEntityTypeLinkHTML();

    /**
     * @param mixed $user
     *
     * @abstract
     */
    public static function getAccessEntitiesForUser($user)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    public function validate(PermissionAccess $pae)
    {
        return true;
    }

    final public static function getByID($peID)
    {
        $obj = CacheLocal::getEntry('permission_access_entity', $peID);
        if ($obj instanceof PermissionAccessEntity) {
            return $obj;
        }
        $db = Database::connection();
        $r = $db->GetRow('select petID, peID from PermissionAccessEntities where peID = ?', array($peID));
        if (is_array($r)) {
            $pt = Type::getByID($r['petID']);
            if (!is_object($pt)) {
                return false;
            }

            $class = '\\Core\\Permission\\Access\\Entity\\' . Core::make('helper/text')->camelcase($pt->getAccessEntityTypeHandle()) . 'Entity';
            $class = core_class($class, $pt->getPackageHandle());
            $obj = Core::make($class);
            $r['petHandle'] = $pt->getAccessEntityTypeHandle();
            $obj->setPropertiesFromArray($r);
            $obj->load();
        }
        CacheLocal::set('permission_access_entity', $peID, $obj);

        return $obj;
    }

    public static function getForUser($user)
    {
        $entities = array();
        $types = Type::getList();
        foreach ($types as $t) {
            $entities = array_merge($entities, $t->getAccessEntitiesForUser($user));
        }

        return $entities;
    }
}
