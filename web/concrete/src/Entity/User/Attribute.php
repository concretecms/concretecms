<?php

namespace Concrete\Core\Entity\User;

use Concrete\Core\Attribute\AttributeInterface;

/**
 * @Entity(repositoryClass="\Concrete\Core\Entity\User\AttributeRepository")
 * @Table(name="UserAttributeKeys")
 */
class Attribute implements AttributeInterface
{

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

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
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttributeKey($attribute)
    {
        $this->attribute_key = $attribute;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyDisplayedInProfile()
    {
        return $this->uakProfileDisplay;
    }

    /**
     * @param mixed $uakProfileDisplay
     */
    public function setAttributeKeyDisplayedInProfile($uakProfileDisplay)
    {
        $this->uakProfileDisplay = $uakProfileDisplay;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableInProfile()
    {
        return $this->uakProfileEdit;
    }

    /**
     * @param mixed $uakProfileEdit
     */
    public function setAttributeKeyEditableInProfile($uakProfileEdit)
    {
        $this->uakProfileEdit = $uakProfileEdit;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyRequiredInProfile()
    {
        return $this->uakProfileEditRequired;
    }

    /**
     * @param mixed $uakProfileEditRequired
     */
    public function setAttributeKeyRequiredInProfile($uakProfileEditRequired)
    {
        $this->uakProfileEditRequired = $uakProfileEditRequired;
    }

    /**
     * @return mixed
     */
    public function isAttributeKeyEditableInRegistration()
    {
        return $this->uakRegisterEdit;
    }

    /**
     * @param mixed $uakRegisterEdit
     */
    public function setAttributeKeyEditableInRegistration($uakRegisterEdit)
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
    public function isDisplayedInMemberList()
    {
        return $this->uakMemberListDisplay;
    }

    /**
     * @param mixed $uakMemberListDisplay
     */
    public function setAttributeKeyDisplayedInMemberList($uakMemberListDisplay)
    {
        $this->uakMemberListDisplay = $uakMemberListDisplay;
    }





}
