<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Foundation\Environment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="ExpressFormFieldSetControls")
 */
abstract class Control implements \JsonSerializable
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="integer")
     */
    protected $position = 0;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $custom_label;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\FieldSet")
     **/
    protected $field_set;


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
    public function getCustomLabel()
    {
        return $this->custom_label;
    }

    /**
     * @param mixed $custom_label
     */
    public function setCustomLabel($custom_label)
    {
        $this->custom_label = $custom_label;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getFieldSet()
    {
        return $this->field_set;
    }

    /**
     * @param mixed $field_set
     */
    public function setFieldSet($field_set)
    {
        $this->field_set = $field_set;
    }


    /**
     * @return \Concrete\Core\Express\Form\Control\RendererInterface
     */
    abstract public function getFormRenderer();

    abstract public function getControlLabel();

    abstract public function getType();

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId()
        ];
    }

    public function getDisplayLabel()
    {
        return $this->getCustomLabel() ?
            $this->getCustomLabel() :
            $this->getControlLabel();
    }

    public function getControlType()
    {
        $manager = \Core::make('express.control.type.manager');
        return $manager->driver($this->getType());
    }

}