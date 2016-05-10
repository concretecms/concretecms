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
     * @Column(type="string", nullable=true)
     */
    protected $akTypeHandle;

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
    public function getAttributeTypeHandle()
    {
        if (isset($this->akTypeHandle)) {
            return $this->akTypeHandle; // By allowing the controllers to set this, we can create attributes that extend the built-in key types
        } else {
            // attempt to determine it dynamically
            $class = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
            $class = substr($class, 0, strpos($class, 'Type'));
            return uncamelcase($class);
        }
    }

    /**
     * @param mixed $akTypeHandle
     */
    public function setAttributeTypeHandle($akTypeHandle)
    {
        $this->akTypeHandle = $akTypeHandle;
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

    public function createController()
    {
        $type = $this->getAttributeType();
        return $type->getController();
    }

    public function getController()
    {
        $controller = $this->createController();

        return $controller;
    }
}
