<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(
 *     name="AttributeKeyTypes"
 * )
 */
abstract class Type
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $akTypeID;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key", inversedBy="key_type")
     * @JoinColumn(name="akID", referencedColumnName="akID")
     */
    protected $key;

    public function getKeyTypeID()
    {
        return $this->akTypeID;
    }

    public function setKeyTypeID($akTypeID)
    {
        $this->akTypeID = $akTypeID;
    }

    /**
     * @return mixed
     */
    public function getAttributeKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setAttributeKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getAttributeType()
    {
        $factory = \Core::make('Concrete\Core\Attribute\TypeFactory');
        return $factory->getByHandle($this->getAttributeTypeHandle());
    }

    /**
     * @param mixed $type
     */
    public function setAttributeType($type)
    {
        $this->type = $type;
    }

    abstract public function createController();
    abstract public function getAttributeTypeHandle();

    public function getController()
    {
        $controller = $this->createController();

        return $controller;
    }
}
