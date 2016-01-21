<?php
namespace Concrete\Core\Entity\Attribute\Key;

/**
 * @Entity(repositoryClass="\Concrete\Core\Entity\User\AttributeRepository")
 * @Table(name="UserAttributeKeys")
 */
class UserKey extends Key
{
    /**
     * @Column(type="boolean")
     */
    protected $uakProfileDisplay = false;

    /**
     * @Column(type="boolean")
     */
    protected $uakProfileEdit = false;

    /**
     * @Column(type="boolean")
     */
    protected $uakProfileEditRequired = false;

    /**
     * @Column(type="boolean")
     */
    protected $uakRegisterEdit = false;

    /**
     * @Column(type="boolean")
     */
    protected $uakRegisterEditRequired = false;

    /**
     * @Column(type="boolean")
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

}
