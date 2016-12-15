<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\Application;

/**
 * @ORM\Entity
 * @ORM\Table(name="atDateTime")
 */
class DateTimeValue extends AbstractValue
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
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
        $result = '';
        $v = $this->value;
        if ($this->value) {
            $app = Application::getFacadeApplication();
            $dh = $app->make('helper/date');
            /* @var \Concrete\Core\Localization\Service\Date $dh */
            $result = $dh->formatDateTime($this->value);
        }

        return $result;
    }
}
