<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="LegacyAttributeValues")
 */
class LegacyValue extends Value
{

    public function __toString()
    {
        return '';
    }

    public function getValue()
    {
        $controller = $this->getAttributeKey()->getController();
        if (method_exists($controller, 'getValue')) {
            $controller->setAttributeValue($this);
            return $controller->getValue();
        }

        return '';
    }


}
