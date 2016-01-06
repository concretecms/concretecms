<?php

namespace Concrete\Core\Entity\Attribute\Value\Value;


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

    /**
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value", inversedBy="value")
     * @JoinColumn(name="avrID", referencedColumnName="avrID")
     **/
    protected $attribute_value;

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->attribute_value;
    }

    /**
     * @param mixed $attribute_value
     */
    public function setAttributeValue($attribute_value)
    {
        $this->attribute_value = $attribute_value;
    }


    public function getDisplaySanitizedValue()
    {
        $controller = $this->getAttributeValue()->getAttributeKey()->getController();
        if (method_exists($controller, 'getDisplaySanitizedValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplaySanitizedValue();
        }
        return $this->getDisplayValue();
    }

    public function getDisplayValue()
    {
        $controller = $this->getAttributeValue()->getAttributeKey()->getController();
        if (method_exists($controller, 'getDisplayValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplayValue();
        }
        return $this;
    }

    public function getSearchIndexValue()
    {
        $controller = $this->getAttributeValue()->getAttributeKey()->getController();
        if (method_exists($controller, 'getSearchIndexValue')) {
            $controller->setAttributeValue($this);
            return $controller->getSearchIndexValue();
        }
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getDisplayValue();
    }

}
