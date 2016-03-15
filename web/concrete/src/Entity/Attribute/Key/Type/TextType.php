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

}
