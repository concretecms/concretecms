<?php

namespace Concrete\Core\Permission\Access\ListItem;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use Concrete\Core\Permission\Duration;

class ListItem extends ConcreteObject
{
    /**
     * @var \Concrete\Core\Permission\Duration
     */
    public $duration;

    /**
     * One of the \Concrete\Core\Permission\Key\Key::ACCESS_TYPE_ constants.
     *
     * @see \Concrete\Core\Permission\Key\Key
     *
     * @var int
     */
    public $accessType;

    /**
     * @var \Concrete\Core\Permission\Access\Entity\Entity
     */
    public $accessEntity;

    /**
     * @var int
     */
    public $paID;

    /**
     * @return int
     */
    public function getPermissionAccessID()
    {
        return $this->paID;
    }

    /**
     * @param int $paID
     */
    public function setPermissionAccessID($paID)
    {
        $this->paID = $paID;
    }

    /**
     * @param int $accessType one of the \Concrete\Core\Permission\Key\Key::ACCESS_TYPE_ constants
     *
     * @see \Concrete\Core\Permission\Key\Key
     */
    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;
    }

    /**
     * @return int one of the \Concrete\Core\Permission\Key\Key::ACCESS_TYPE_ constants
     *
     * @see \Concrete\Core\Permission\Key\Key
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * @param int|null $pdID
     */
    public function loadPermissionDurationObject($pdID)
    {
        if ($pdID > 0) {
            $this->duration = Duration::getByID($pdID);
        }
    }

    /**
     * @param int $peID
     */
    public function loadAccessEntityObject($peID)
    {
        if ($peID > 0) {
            $this->accessEntity = PermissionAccessEntity::getByID($peID);
        }
    }

    /**
     * @return \Concrete\Core\Permission\Access\Entity\Entity
     */
    public function getAccessEntityObject()
    {
        return $this->accessEntity;
    }

    /**
     * @return \Concrete\Core\Permission\Duration
     */
    public function getPermissionDurationObject()
    {
        return $this->duration;
    }

    /**
     * @param \Concrete\Core\Permission\Access\Entity\Entity $accessEntity
     */
    public function setAccessEntityObject($accessEntity)
    {
        $this->accessEntity = $accessEntity;
    }

    /**
     * @param \Concrete\Core\Permission\Duration $duration
     */
    public function setPermissionDurationObject($duration)
    {
        $this->duration = $duration;
    }
}
