<?php
namespace Concrete\Core\Entity\Express;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ExpressForms")
 */
class Form
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @OneToMany(targetEntity="FieldSet", mappedBy="form", cascade={"persist", "remove"})
     * @OrderBy({"position" = "ASC"})
     */
    protected $field_sets;

    /**
     * @ManyToOne(targetEntity="Entity")
     **/
    protected $entity;

    public function __construct()
    {
        $this->field_sets = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFieldSets()
    {
        return $this->field_sets;
    }

    /**
     * @param mixed $field_sets
     */
    public function setFieldSets($field_sets)
    {
        $this->field_sets = $field_sets;
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

    public function getControls()
    {
        $controls = array();
        foreach ($this->getFieldSets() as $set) {
            foreach ($set->getControls() as $control) {
                $controls[] = $control;
            }
        }

        return $controls;
    }
}
