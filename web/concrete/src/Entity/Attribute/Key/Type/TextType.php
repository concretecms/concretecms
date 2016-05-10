<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\TextValue;

/**
 * @Entity
 * @Table(name="TextAttributeKeyTypes")
 */
class TextType extends Type
{
    public function getAttributeValue()
    {
        return new TextValue();
    }

    /**
     * @Column(type="string")
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
