<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atSelectOptions")
 */
class SelectValueOption
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avSelectOptionID;

    /**
     * @ORM\ManyToOne(targetEntity="SelectValueOptionList", inversedBy="options")
     * @ORM\JoinColumn(name="avSelectOptionListID", referencedColumnName="avSelectOptionListID")
     */
    protected $list;

    /**
     * @ORM\ManyToMany(targetEntity="SelectValue", mappedBy="selectedOptions", cascade={"remove"})
     */
    protected $values;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isEndUserAdded = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $isDeleted = false;

    /**
     * @ORM\Column(type="integer")
     */
    protected $displayOrder = 0;

    /**
     * @ORM\Column(type="string")
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
    public function isOptionDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsOptionDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
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
        return (string)$this->getSelectAttributeOptionValue();
    }

    public function getSelectAttributeOptionDisplayValue($format = 'html')
    {
        $value = tc('SelectAttributeValue', $this->getSelectAttributeOptionValue());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

}
