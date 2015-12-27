<?php

namespace Concrete\Core\Entity\Page;

use Concrete\Core\Attribute\AttributeInterface;

/**
 * @Entity
 * @Table(name="CollectionAttributeKeys")
 */
class Attribute implements AttributeInterface
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
