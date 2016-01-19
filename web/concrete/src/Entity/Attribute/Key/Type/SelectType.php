<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;

/**
 * @Entity
 * @Table(name="SelectAttributeKeyTypes")
 */
class SelectType extends Type
{
    /**
     * @OneToOne(targetEntity="Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList", cascade={"all"})
     * @JoinColumn(name="avSelectOptionListID", referencedColumnName="avSelectOptionListID")
     */
    protected $list;

    /**
     * @return mixed
     */
    public function getOptionList()
    {
        return $this->list;
    }

    /**
     * @param mixed $list
     */
    public function setOptionList($list)
    {
        $this->list = $list;
    }

    public function getAttributeValue()
    {
        return new SelectValue();
    }

    /**
     * @Column(type="boolean")
     */
    protected $allowMultipleValues = false;

    /**
     * @Column(type="boolean")
     */
    protected $allowOtherValues = false;

    /**
     * @Column(type="string")
     */
    protected $displayOrder = 'display_asc';

    /**
     * @return mixed
     */
    public function getAllowMultipleValues()
    {
        return $this->allowMultipleValues;
    }

    /**
     * @param mixed $allowMultipleValues
     */
    public function setAllowMultipleValues($allowMultipleValues)
    {
        $this->allowMultipleValues = $allowMultipleValues;
    }

    /**
     * @return mixed
     */
    public function getAllowOtherValues()
    {
        return $this->allowOtherValues;
    }

    /**
     * @param mixed $allowOtherValues
     */
    public function setAllowOtherValues($allowOtherValues)
    {
        $this->allowOtherValues = $allowOtherValues;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * @param mixed $displayOrder
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }

    public function createController()
    {
        $controller = \Core::make('\Concrete\Attribute\Select\Controller');
        $controller->setAttributeType($this->getAttributeType());

        return $controller;
    }
}
