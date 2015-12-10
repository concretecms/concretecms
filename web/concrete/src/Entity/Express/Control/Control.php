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
abstract class Control
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
     * @Column(type="text")
     */
    protected $custom_label;


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
     * @return \Concrete\Core\Express\Form\Control\RendererInterface
     */
    abstract public function getFormRenderer();



}