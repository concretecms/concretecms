<?php
namespace Concrete\Core\Entity\Express;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="ExpressEntityAssociations")
 */
abstract class Association
{
    abstract public function getAssociationBuilder();

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Entity")
     **/
    protected $source_entity;

    /**
     * @OneToOne(targetEntity="Entity")
     **/
    protected $target_entity;

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Express\Control\AssociationControl", mappedBy="association", cascade={"remove"})
     */
    protected $controls;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $target_property_name;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $inversed_by_property_name;

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
    public function getTargetPropertyName()
    {
        return $this->target_property_name;
    }

    /**
     * @param mixed $name
     */
    public function setTargetPropertyName($target_property_name)
    {
        $this->target_property_name = $target_property_name;
    }

    /**
     * @return mixed
     */
    public function getInversedByPropertyName()
    {
        return $this->inversed_by_property_name;
    }

    /**
     * @param mixed $inversed_by_property_name
     */
    public function setInversedByPropertyName($inversed_by_property_name)
    {
        $this->inversed_by_property_name = $inversed_by_property_name;
    }

    /**
     * @return mixed
     */
    public function getSourceEntity()
    {
        return $this->source_entity;
    }

    /**
     * @param mixed $source_entity
     */
    public function setSourceEntity($source_entity)
    {
        $this->source_entity = $source_entity;
    }

    /**
     * @return mixed
     */
    public function getTargetEntity()
    {
        return $this->target_entity;
    }

    /**
     * @param mixed $target_entity
     */
    public function setTargetEntity($target_entity)
    {
        $this->target_entity = $target_entity;
    }

    public function getComputedTargetPropertyName()
    {
        if ($this->getTargetPropertyName()) {
            return $this->getTargetPropertyName();
        } else {
            return uncamelcase($this->getTargetEntity()->getName());
        }
    }

    public function getComputedInversedByPropertyName()
    {
        if ($this->getInversedByPropertyName()) {
            return $this->getInversedByPropertyName();
        } else {
            return uncamelcase($this->getSourceEntity()->getName());
        }
    }

    abstract public function getFormatter();
}
