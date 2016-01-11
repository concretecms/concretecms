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

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Attribute\Type")
     * @JoinColumn(name="atID", referencedColumnName="atID")
     **/
    protected $type;

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
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setAttributeType($type)
    {
        $this->type = $type;
    }

    abstract public function createController();

    public function getController()
    {
        $controller = $this->createController();

        return $controller;
    }
}
