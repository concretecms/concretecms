<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\User\AttributeRepository")
 * @ORM\Table(name="UserAttributeKeys")
 */
class UserKey extends Key
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakProfileDisplay = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakProfileEdit = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakProfileEditRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakRegisterEdit = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakRegisterEditRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $uakMemberListDisplay = false;

    /**
     * @return mixed
     */
    public function isAttributeKeyDisplayedOnProfile()
    {
        return $this->uakProfileDisplay;
    }

    /**
     * @ORM\OneToMany(targetEntity="UserKeyPerUserGroup",mappedBy="userAttributeKey",orphanRemoval=true,cascade={"all"})
     * @var ArrayCollection
     */
    protected $userKeyPerUserGroups;

    /**
     * @return ArrayCollection
     */
    public function getUserKeyPerUserGroups()
    {
        if (empty($this->userKeyPerUserGroups)) {
            $this->userKeyPerUserGroups = new ArrayCollection();
        }
        return $this->userKeyPerUserGroups;
    }

    /**
     * @param ArrayCollection $userKeyPerUserGroups
     * @return $this
     */
    public function setUserKeyPerUserGroups($userKeyPerUserGroups)
    {
        if (empty($this->userKeyPerUserGroups)) {
            $this->userKeyPerUserGroups = new ArrayCollection();
        } else {
            $this->userKeyPerUserGroups->clear();
        }
        foreach ($userKeyPerUserGroups as $userKeyPerUserGroup) {
            $this->userKeyPerUserGroups->add($userKeyPerUserGroup);
        }
        return $this;
    }


    /**
     * @param UserKeyPerUserGroup $userKeyPerUserGroup
     * @return $this
     */
    public function addUserKeyPerUserGroups(UserKeyPerUserGroup $userKeyPerUserGroup)
    {
        if (empty($this->userKeyPerUserGroups)) {
            $this->userKeyPerUserGroups = new ArrayCollection();
        }
        if (!$this->userKeyPerUserGroups->contains($userKeyPerUserGroup)) {
            $this->userKeyPerUserGroups->add($userKeyPerUserGroup);
        }
        return $this;
    }


    public function removeUserKeyPerUserGroups(UserKeyPerUserGroup $userKeyPerUserGroup)
    {
        if (empty($this->userKeyPerUserGroups)) {
            $this->userKeyPerUserGroups = new ArrayCollection();
        }
        if ($this->userKeyPerUserGroups->contains($userKeyPerUserGroup)) {
            $this->userKeyPerUserGroups->removeElement($userKeyPerUserGroup);
        }
    }


    /**
     * @param mixed $uakProfileDisplay
     */
    public function setAttributeKeyDisplayedOnProfile($uakProfileDisplay)
    {
        $this->uakProfileDisplay = $uakProfileDisplay;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableOnProfile()
    {
        return $this->uakProfileEdit;
    }

    /**
     * @param mixed $uakProfileEdit
     */
    public function setAttributeKeyEditableOnProfile($uakProfileEdit)
    {
        $this->uakProfileEdit = $uakProfileEdit;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyRequiredOnProfile()
    {
        return $this->uakProfileEditRequired;
    }

    /**
     * @param mixed $uakProfileEditRequired
     */
    public function setAttributeKeyRequiredOnProfile($uakProfileEditRequired)
    {
        $this->uakProfileEditRequired = $uakProfileEditRequired;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableOnRegister()
    {
        return $this->uakRegisterEdit;
    }

    /**
     * @param mixed $uakRegisterEdit
     */
    public function setAttributeKeyEditableOnRegister($uakRegisterEdit)
    {
        $this->uakRegisterEdit = $uakRegisterEdit;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyRequiredOnRegister()
    {
        return $this->uakRegisterEditRequired;
    }

    /**
     * @param mixed $uakRegisterEditRequired
     */
    public function setAttributeKeyRequiredOnRegister($uakRegisterEditRequired)
    {
        $this->uakRegisterEditRequired = $uakRegisterEditRequired;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyDisplayedOnMemberList()
    {
        return $this->uakMemberListDisplay;
    }

    /**
     * @param mixed $uakMemberListDisplay
     */
    public function setAttributeKeyDisplayedOnMemberList($uakMemberListDisplay)
    {
        $this->uakMemberListDisplay = $uakMemberListDisplay;
    }

    public function getAttributeKeyCategoryHandle()
    {
        return 'user';
    }

    /**
     * @return \Group[]
     */
    public function getAssociatedGroups()
    {
        $groups = array();
        if ($this->userKeyPerUserGroups->count() > 0) {
            /**
             * @var $userKeyPerUserGroup UserKeyPerUserGroup
             */
            foreach ($this->userKeyPerUserGroups as $userKeyPerUserGroup) {
                $group = $userKeyPerUserGroup->getGroup();
                if (is_object($group)) {
                    $groups[$group->getGroupID()] = $group;
                }
            }
        }
        return $groups;
    }

    /**
     * Method that return key configuration for specific associated group
     * @param \Group $group
     * @return UserKeyPerUserGroup|null
     */
    public function getKeyConfigurationForGroup(\Group $group)
    {
        $userKeyPerUserGroup = null;
        if ($this->userKeyPerUserGroups->count() > 0) {
            foreach ($this->userKeyPerUserGroups as $userKeyPerUserGroup1) {
                if ($group->getGroupID() == $userKeyPerUserGroup1->getGID()) {
                    $userKeyPerUserGroup = $userKeyPerUserGroup1;
                    break;
                }
            }
        }
        return $userKeyPerUserGroup;
    }

    /**
     * Method that verify if the attribute is required for groups received as parameter.If $userGroups is empty we return common configuration
     * Note: the attribute key  is considered required if is found required for one of searched group
     * @var \Group[]$userGroups
     * @return boolean
     */
    public function isAttributeKeyRequiredOnProfileForUserGroups($userGroups)
    {
        return $this->isAttributeKeyRequiredForUserGroupsSharedCode($userGroups, "uakProfileEditRequired");
    }


    /**
     * Method that verify if the attribute is required for groups received as parameter.If $userGroups is empty we return common configuration
     * Note: the attribute key  is considered required if is found required for one of searched group
     * @var \Group[]$userGroups
     * @return boolean
     */
    public function isAttributeKeyRequiredOnRegisterForUserGroups($userGroups)
    {
        return $this->isAttributeKeyRequiredForUserGroupsSharedCode($userGroups, "uakRegisterEditRequired");
    }

    /**
     * @param $userGroups \Group[]
     * @param $fieldName
     * @return bool
     */
    private function isAttributeKeyRequiredForUserGroupsSharedCode($userGroups, $fieldName)
    {
        $methodName=null;
        switch ($fieldName) {
            case "uakProfileEditRequired":
                $methodName="isAttributeKeyRequiredOnProfile";
                break;
            default:// is field "uakRegisterEditRequired":
                $methodName="isAttributeKeyRequiredOnRegister";
            break;
        }
        if (count($userGroups)>0) {
            foreach ($userGroups as $group) {
                if (count($this->userKeyPerUserGroups)>0) {
                    /**
                     * @var $userKeyPerUserGroup UserKeyPerUserGroup
                     */
                    foreach ($this->userKeyPerUserGroups as $userKeyPerUserGroup) {
                        if ($group->getGroupID()==$userKeyPerUserGroup->getGID() && $userKeyPerUserGroup->{$methodName}()) {
                            return true;
                        }
                    }
                } else {
                    goto a;
                }
            }
            return false;
        }
        a:
        return $this->{$fieldName};
    }
}
