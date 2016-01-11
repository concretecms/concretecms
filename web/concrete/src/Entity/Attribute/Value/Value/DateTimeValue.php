<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

/**
 * @Entity
 * @Table(name="DateTimeAttributeValues")
 */
class DateTimeValue extends Value
{
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $value = '';

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
