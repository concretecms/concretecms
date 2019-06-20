<?php

namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atUserGroupSettings")
 */
class UserGroupSettings extends Settings
{
    const GROUP_SELECTION_METHOD_ALL = 'A';
    const GROUP_SELECTION_METHOD_IN_GROUP = 'G';
    const GROUP_SELECTION_METHOD_PERMISSIONS = 'P';

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $akDisplayGroupsBeneathSpecificParent = false;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $akDisplayGroupsBeneathParentID = 0;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $akGroupSelectionMethod = false;

    /**
     * @return mixed
     */
    public function displayGroupsBeneathSpecificParent()
    {
        return $this->akDisplayGroupsBeneathSpecificParent;
    }

    /**
     * @param mixed $akDisplayGroupsBeneathSpecificParent
     */
    public function setDisplayGroupsBeneathSpecificParent($akDisplayGroupsBeneathSpecificParent)
    {
        $this->akDisplayGroupsBeneathSpecificParent = $akDisplayGroupsBeneathSpecificParent;
    }

    /**
     * @return mixed
     */
    public function getDisplayGroupsBeneathParentID()
    {
        return $this->akDisplayGroupsBeneathParentID;
    }

    /**
     * @param mixed $akDisplayGroupsBeneathParentID
     */
    public function setDisplayGroupsBeneathParentID($akDisplayGroupsBeneathParentID)
    {
        $this->akDisplayGroupsBeneathParentID = $akDisplayGroupsBeneathParentID;
    }

    /**
     * @return mixed
     */
    public function getGroupSelectionMethod()
    {
        return $this->akGroupSelectionMethod;
    }

    /**
     * @param mixed $akGroupSelectionMethod
     */
    public function setGroupSelectionMethod($akGroupSelectionMethod)
    {
        $this->akGroupSelectionMethod = $akGroupSelectionMethod;
    }
}
