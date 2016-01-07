<?php

namespace Concrete\Core\Entity\Attribute\Value;

use Concrete\Core\Attribute\AttributeValueInterface;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="AttributeValues")
 */
abstract class Value implements AttributeValueInterface
{

    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avrID;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\Value", cascade={"persist", "remove"})
     * @JoinColumn(name="avID", referencedColumnName="avID")
     **/
    protected $value;

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

    public function getAttributeTypeObject()
    {
        return $this->getAttributeKey()->getAttributeType();
    }

    public function getValue($mode = false)
    {
        $value = $this->value;
        if (is_object($value)) {
            if ($mode != false) {
                $controller = $this->getAttributeKey()->getController();
                $modes = func_get_args();
                foreach ($modes as $mode) {
                    $method = 'get' . camelcase($mode) . 'Value';
                    if (method_exists($controller, $method)) {
                        $controller->setAttributeValue($value);
                        return $controller->{$method}();
                    }
                }
            }
        }
        return $value;
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
        return (string) $this->getValue()->getDisplayValue();
    }

}
