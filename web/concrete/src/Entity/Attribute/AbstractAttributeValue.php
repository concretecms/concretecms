<?php

namespace Concrete\Core\Entity\Attribute;

/**
 * @MappedSuperClass
 */
abstract class AbstractAttributeValue
{

    /**
     * @Id
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     **/
    protected $attribute_value;

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


    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        $value = $this->attribute_value;
        if (is_object($value)) {
            $value->setAttributeKey($this->getAttributeKey());
        }
        return $value;
    }

    /**
     * @param mixed $attribute_value
     */
    public function setAttributeValue($attribute_value)
    {
        $this->attribute_value = $attribute_value;
    }

}
