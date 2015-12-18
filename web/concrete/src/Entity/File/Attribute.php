<?php

namespace Concrete\Core\Entity\File;

/**
 * @Entity
 * @Table(name="FileAttributeKeys")
 */
class Attribute
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
     * @param mixed $attribute
     */
    public function setAttributeKey($attribute)
    {
        $this->attribute_key = $attribute;
    }





}
