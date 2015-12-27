<?php

namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Entity\Attribute\Value\SelectValue;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @Entity
 * @Table(name="SelectAttributeKeys")
 */
class SelectKey extends Key
{

    /**
     * @OneToMany(targetEntity="Concrete\Core\Entity\Attribute\Value\SelectValueOption", mappedBy="key", cascade={"all"})
     */
    protected $options;

    public function getTypeHandle()
    {
        return 'select';
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

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function createController()
    {
        $controller = new \Concrete\Attribute\Select\Controller($this->getAttributeType());
        return $controller;
    }

}
