<?php
namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(name="SelectAttributeValueOptions")
 */
class SelectValueOption
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avSelectOptionID;

    /**
     * @ManyToOne(targetEntity="SelectValueOptionList", inversedBy="options")
     * @JoinColumn(name="avSelectOptionListID", referencedColumnName="avSelectOptionListID")
     */
    protected $list;

    /**
     * @ManyToMany(targetEntity="SelectValue", mappedBy="selectedOptions", cascade={"remove"})
     */
    protected $values;

    /**
     * @Column(type="boolean")
     */
    protected $isEndUserAdded = false;

    /**
     * @Column(type="integer")
     */
    protected $displayOrder = 0;

    /**
     * @Column(type="string")
     */
    protected $value = '';

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

    /**
     * @return mixed
     */
    public function getSelectAttributeOptionID()
    {
        return $this->avSelectOptionID;
    }

    /**
     * @param mixed $avSelectOptionID
     */
    public function setSelectAttributeOptionID($avSelectOptionID)
    {
        $this->avSelectOptionID = $avSelectOptionID;
    }

    /**
     * @return mixed
     */
    public function isEndUserAdded()
    {
        return $this->isEndUserAdded;
    }

    /**
     * @param mixed $isEndUserAdded
     */
    public function setIsEndUserAdded($isEndUserAdded)
    {
        $this->isEndUserAdded = $isEndUserAdded;
    }

    /**
     * @return mixed
     */
    public function getSelectAttributeOptionValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setSelectAttributeOptionValue($value)
    {
        $this->value = $value;
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

    public function __toString()
    {
        return (string) $this->getSelectAttributeOptionValue();
    }



}
