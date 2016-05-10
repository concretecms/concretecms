<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

/**
 * @Entity
 * @Table(name="TextAttributeValues")
 */
class TextValue extends Value
{
    /**
     * @Column(type="string", nullable=true)
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

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
