<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

class SelectValueUsedOption
{

    protected $value = '';
    protected $avSelectOptionID;
    protected $count = 0;

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
     * @return int
     */
    public function getSelectAttributeOptionUsageCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setSelectAttributeOptionUsageCount($count)
    {
        $this->count = $count;
    }

    public function getSelectAttributeOptionDisplayValue()
    {
        return $this->getSelectAttributeOptionValue();
    }



}
