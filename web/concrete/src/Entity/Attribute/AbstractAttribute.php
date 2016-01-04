<?php

namespace Concrete\Core\Entity\Attribute;

use Concrete\Core\Attribute\AttributeInterface;
use Concrete\Core\Attribute\AttributeKeyInterface;

/**
 * @MappedSuperClass
 */
abstract class AbstractAttribute implements AttributeKeyInterface, AttributeInterface
{

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key")
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
    public function setAttributeKey(AttributeKeyInterface $attribute_key)
    {
        $this->attribute_key = $attribute_key;
    }


    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->getAttributeKey(), $name), $arguments);
    }

    public function getAttributeKeyID()
    {
        return $this->attribute_key->getAttributeKeyID();
    }

    public function getAttributeKeyHandle()
    {
        return $this->attribute_key->getAttributeKeyHandle();
    }

    public function getAttributeType()
    {
        return $this->attribute_key->getAttributeType();
    }

    public function isAttributeKeySearchable()
    {
        return $this->attribute_key->isAttributeKeySearchable();
    }

    public function getController()
    {
        return $this->attribute_key->getController();
    }





}
