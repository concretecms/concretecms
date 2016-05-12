<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TextValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="TextAttributeKeyTypes")
 */
class TextType extends Type
{
    public function getAttributeValue()
    {
        return new TextValue();
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $akTextPlaceholder = '';

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->akTextPlaceholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->akTextPlaceholder = $placeholder;
    }
}
