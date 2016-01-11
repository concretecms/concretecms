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
     * @OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key", cascade={"persist", "remove"})
     * @JoinColumn(name="akID", referencedColumnName="akID")
     **/
    protected $attribute_key;

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
     * @return \Concrete\Core\Entity\Attribute\Key\Key
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

    public function getAttributeKeyID()
    {
        return $this->attribute->getAttributeKeyID();
    }

    public function getAttributeType()
    {
        return $this->attribute->getAttributeType();
    }

    public function getController()
    {
        return $this->attribute->getController();
    }

    public function getAttributeKeyHandle()
    {
        return $this->attribute->getAttributeKeyHandle();
    }

    public function isAttributeKeySearchable()
    {
        return $this->attribute->isAttributeKeySearchable();
    }

    public function __call($nm, $args)
    {
        if (method_exists($this->attribute, $nm)) {
            return call_user_func_array(array($this->attribute, $nm), $args);
        }
    }
}
