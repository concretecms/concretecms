<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(
 *     name="AttributeKeyTypes"
 * )
 */
abstract class Type
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $akTypeID;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $akTypeHandle;

    /**
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\Attribute\Key\Key", inversedBy="key_type")
     * @ORM\JoinColumn(name="akID", referencedColumnName="akID")
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

    public function mergeAndPersist(EntityManagerInterface $entityManager)
    {
        $key_type = $entityManager->merge($this);
        $entityManager->persist($key_type);
        return $key_type;
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
