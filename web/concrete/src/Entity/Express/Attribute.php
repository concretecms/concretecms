<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\AttributeKeyInterface;

/**
 * @Entity
 * @Table(name="ExpressEntityAttributes")
 */
class Attribute implements AttributeKeyInterface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Entity")
     **/
    protected $entity;

    /**
     * @OneToOne(targetEntity="\Concrete\Core\Entity\AttributeKey\AttributeKey", cascade={"persist", "remove"})
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return \Concrete\Core\Entity\AttributeKey\AttributeKey
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

    public function getAttributeKeyID()
    {
        return $this->attribute->getAttributeKeyID();
    }

    public function getAttributeType()
    {
        return $this->attribute->getAttributeType();
    }

    public function getAttributeKeyHandle()
    {
        return $this->attribute->getAttributeKeyHandle();
    }

    public function __call($nm, $args)
    {
        if (method_exists($this->attribute, $nm)) {
            return call_user_func_array(array($this->attribute, $nm), $args);
        }
    }

}