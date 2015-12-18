<?php
namespace Concrete\Core\Entity\Attribute\Value;

/**
 * @Entity
 * @Table(name="TopicsAttributeValues")
 */
class TopicsValue extends Value
{
    /**
     * @Column(type="json_array")
     */
    protected $value;

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

    public function getFormatter()
    {
        return new SelectFormatter($this);
    }

    public function getPublisher()
    {
        return new StandardPublisher();
    }
}
