<?php

namespace Concrete\Core\Entity\Attribute\Value;

use Concrete\Core\Entity\Attribute\Key\Key;


/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="AttributeValues")
 */
abstract class Value
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    protected $attribute_key;

    public function setAttributeKey(Key $key)
    {
        $this->attribute_key = $key;
    }

    public function getDisplaySanitizedValue()
    {
        $controller = $this->attribute_key->getController();
        if (method_exists($controller, 'getDisplaySanitizedValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplaySanitizedValue();
        }
        return $this;
    }

    public function getDisplayValue()
    {
        $controller = $this->attribute_key->getController();
        if (method_exists($controller, 'getDisplayValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplayValue();
        }
        return $this;
    }

    public function getSearchIndexValue()
    {
        $controller = $this->attribute_key->getController();
        if (method_exists($controller, 'getSearchIndexValue')) {
            $controller->setAttributeValue($this);
            return $controller->getSearchIndexValue();
        }
        return $this;
    }

    public function __toString()
    {
        return $this->getDisplayValue();
    }

}
