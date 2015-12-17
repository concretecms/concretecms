<?php

namespace Concrete\Core\Entity\User;

/**
 * @Entity
 * @Table(name="UserAttributeKeys")
 */
class Attribute
{

    /**
     * @Id
     * @OneToOne(targetEntity="\Concrete\Core\Entity\AttributeKey\AttributeKey")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute;

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }





}
