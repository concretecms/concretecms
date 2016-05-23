<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TextareaValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="TextareaAttributeTypes")
 */
class TextareaType extends Type
{
    public function getAttributeValue()
    {
        return new TextareaValue();
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $akTextareaDisplayMode = '';

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->akTextareaDisplayMode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->akTextareaDisplayMode = $mode;
    }

}
