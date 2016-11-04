<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

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
        $v = $this->value;
        if (empty($v)) {
            return '';
        }
        $dh = \Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        if ($this->akDateDisplayMode == 'date') {
            // Don't use user's timezone to avoid showing wrong dates
            return $dh->formatDate($v, false, 'system');
        } else {
            return $dh->formatDateTime($v);
        }
    }
}
