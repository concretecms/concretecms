<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="UserAttributeKeysPerUserGroups")
 */
class UserKeyPerUserGroup
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     */
    protected $id;


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
     * @ORM\Column(name="gID",type="integer",length=10,options={"unsigned":true})
     */
    protected $gID ;

    /**
     * @ORM\ManyToOne(targetEntity="UserKey",inversedBy="userKeyPerUserGroups")
     * @ORM\JoinColumn(name="akID",referencedColumnName="akID")
     */
    protected $userAttributeKey;

    /**
     * @return mixed
     */
    public function isAttributeKeyDisplayedOnProfile()
    {
        return $this->uakProfileDisplay;
    }

    /**
     * @param $uakProfileDisplay
     * @return $this
     */
    public function setAttributeKeyDisplayedOnProfile($uakProfileDisplay)
    {
        $this->uakProfileDisplay = $uakProfileDisplay;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableOnProfile()
    {
        return $this->uakProfileEdit;
    }

    /**
     * @param $uakProfileEdit
     * @return $this
     */
    public function setAttributeKeyEditableOnProfile($uakProfileEdit)
    {
        $this->uakProfileEdit = $uakProfileEdit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyRequiredOnProfile()
    {
        return $this->uakProfileEditRequired;
    }

    /**
     * @param $uakProfileEditRequired
     * @return $this
     */
    public function setAttributeKeyRequiredOnProfile($uakProfileEditRequired)
    {
        $this->uakProfileEditRequired = $uakProfileEditRequired;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableOnRegister()
    {
        return $this->uakRegisterEdit;
    }

    /**
     * @param $uakRegisterEdit
     * @return $this
     */
    public function setAttributeKeyEditableOnRegister($uakRegisterEdit)
    {
        $this->uakRegisterEdit = $uakRegisterEdit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyRequiredOnRegister()
    {
        return $this->uakRegisterEditRequired;
    }

    /**
     * @param $uakRegisterEditRequired
     * @return $this
     */
    public function setAttributeKeyRequiredOnRegister($uakRegisterEditRequired)
    {
        $this->uakRegisterEditRequired = $uakRegisterEditRequired;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyDisplayedOnMemberList()
    {
        return $this->uakMemberListDisplay;
    }

    /**
     * @param $uakMemberListDisplay
     * @return $this
     */
    public function setAttributeKeyDisplayedOnMemberList($uakMemberListDisplay)
    {
        $this->uakMemberListDisplay = $uakMemberListDisplay;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGID()
    {
        return $this->gID;
    }

    /**
     * @param mixed $gID
     * @return UserKeyPerUserGroup
     */
    public function setGID($gID)
    {
        $this->gID = $gID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserAttributeKey()
    {
        return $this->userAttributeKey;
    }

    /**
     * @param mixed $userAttributeKey
     * @return UserKeyPerUserGroup
     */
    public function setUserAttributeKey($userAttributeKey)
    {
        $this->userAttributeKey = $userAttributeKey;
        return $this;
    }

    /**
     * @return \Concrete\Core\User\Group\Group
     */
    public function getGroup()
    {
        return \Group::getByID($this->gID);
    }
}
