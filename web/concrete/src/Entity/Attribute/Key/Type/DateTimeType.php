<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\DateTimeValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="DateTimeAttributeKeyTypes")
 */
class DateTimeType extends Type
{
    public function getAttributeValue()
    {
        return new DateTimeValue();
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $akDateDisplayMode = '';

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->akDateDisplayMode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->akDateDisplayMode = $mode;
    }

}
