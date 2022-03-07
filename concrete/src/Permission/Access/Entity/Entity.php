<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Facade;
use Database;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use CacheLocal;
use Core;
use RuntimeException;

abstract class Entity extends ConcreteObject
{

    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var int
     */
    public $petID;

    /**
     * @var string
     */
    public $petHandle;

    /**
     * @var int
     */
    public $peID;

    /**
     * @param int $petID
     */
    public function setAccessEntityTypeID($petID)
    {
        $this->petID = $petID;
    }

    /**
     * @param string $petHandle
     */
    public function setAccessEntityTypeHandle($petHandle)
    {
        $this->petHandle = $petHandle;
    }

    /**
     * @param int $peID
     */
    public function setAccessEntityID($peID)
    {
        $this->peID = $peID;
    }
    
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

    abstract public function load();
    
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
        $entity = CacheLocal::getEntry('permission_access_entity', $peID);
        if ($entity instanceof self) {
            return $entity;
        }
        $db = Database::connection();
        $r = $db->GetRow('select petID, peID from PermissionAccessEntities where peID = ?', array($peID));
        if (is_array($r)) {
            $pt = Type::getByID($r['petID']);
            if (!is_object($pt)) {
                return false;
            }
            
            $app = Facade::getFacadeApplication();
            $entity = $app->make('permission/access/entity/factory')->createEntity($pt);

            /**
             * @var $entity Entity
             */
            $entity->setAccessEntityID($r['peID']);
            $entity->setAccessEntityTypeID($r['petID']);
            $entity->setAccessEntityTypeHandle($pt->getAccessEntityTypeHandle());
            $entity->load();
        }

        CacheLocal::set('permission_access_entity', $peID, $entity);

        return $entity;
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
