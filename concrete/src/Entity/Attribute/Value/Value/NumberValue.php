<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\Application;

/**
 * @ORM\Entity
 * @ORM\Table(name="atNumber")
 */
class NumberValue extends AbstractValue
{
    /**
     * @ORM\Column(type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $value = null;

    /**
     * @return string|null
     */
    public function getValue()
    {
        if ($this->value === null) {
            return null;
        } else {
            return Application::getFacadeApplication()->make('helper/number')->trim($this->value);
        }
    }

    /**
     * @param string|float|int|null $value
     */
    public function setValue($value)
    {
        if ($value === null || $value === '') {
            $this->value = null;
        } else {
            $this->value = (string) $value;
        }
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
