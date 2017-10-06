<?php
namespace Concrete\Core\Permission\Access\ListItem;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Duration;

class ListItem extends ConcreteObject
{
    /** @var Duration */
    public $duration;
    public $accessType;

    /**
     * @var $accessEntity Entity
     */
    public $accessEntity;

    public $paID;

    public function getPermissionAccessID()
    {
        return $this->paID;
    }
    public function setPermissionAccessID($paID)
    {
        $this->paID = $paID;
    }

    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;
    }

    public function getAccessType()
    {
        return $this->accessType;
    }

    public function loadPermissionDurationObject($pdID)
    {
        if ($pdID > 0) {
            $pd = Duration::getByID($pdID);
            $this->duration = $pd;
        }
    }

    public function loadAccessEntityObject($peID)
    {
        if ($peID > 0) {
            $pe = PermissionAccessEntity::getByID($peID);
            $this->accessEntity = $pe;
        }
    }

    public function getAccessEntityObject()
    {
        return $this->accessEntity;
    }
    public function getPermissionDurationObject()
    {
        return $this->duration;
    }

    public function setAccessEntityObject($accessEntity)
    {
        $this->accessEntity = $accessEntity;
    }

    public function setPermissionDurationObject($duration)
    {
        $this->duration = $duration;
    }

}
