<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atBoolean")
 */
class BooleanValue extends AbstractValue
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected $value = false;

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
        return ($this->value) ? t('Yes') : t('No');
    }
}
