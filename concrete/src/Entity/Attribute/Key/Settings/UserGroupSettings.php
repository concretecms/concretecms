<?php
namespace Concrete\Core\Entity\Attribute\Key\Settings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atUserGroupSettings")
 */
class UserGroupSettings extends Settings
{
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $akDisplayGroupsBeneathSpecificParent = false;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $akDisplayGroupsBeneathParentID = 0;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $akAllowSelectionFromMyGroupsOnly = false;

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
    public function allowSelectionFromMyGroupsOnly()
    {
        return $this->akAllowSelectionFromMyGroupsOnly;
    }

    /**
     * @param mixed $akAllowSelectionFromMyGroupsOnly
     */
    public function setAllowSelectionFromMyGroupsOnly($akAllowSelectionFromMyGroupsOnly)
    {
        $this->akAllowSelectionFromMyGroupsOnly = $akAllowSelectionFromMyGroupsOnly;
    }





}
