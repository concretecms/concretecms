<?php

namespace Concrete\Core\Entity\Attribute\Value\Value;


/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="AttributeValueValues")
 */
abstract class Value
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avID;

    /**
     * We don't go back to attribute value because multiple object attribute values
     * can map to the same attribute value value – so instead we just duplicate the attribute
     * key in this table so it's easy for us to know which controller we should use.
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->attribute_key;
    }

    /**
     * @param mixed $attribute_key
     */
    public function setAttributeKey($attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }

    public function getDisplaySanitizedValue()
    {
        $controller = $this->getAttributeKey()->getController();
        if (method_exists($controller, 'getDisplaySanitizedValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplaySanitizedValue();
        }
        return $this->getDisplayValue();
    }

    public function getDisplayValue()
    {
        $controller = $this->getAttributeKey()->getController();
        if (method_exists($controller, 'getDisplayValue')) {
            $controller->setAttributeValue($this);
            return $controller->getDisplayValue();
        }
        return $this;
    }

    public function getSearchIndexValue()
    {
        $controller = $this->getAttributeKey()->getController();
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
