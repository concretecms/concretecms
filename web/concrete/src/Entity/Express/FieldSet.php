<?php

namespace Concrete\Core\Entity\Express;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSets")
 */
class FieldSet
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
     * @Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ManyToOne(targetEntity="Form")
     **/
    protected $form;


    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Express\Control\Control", mappedBy="field_set", cascade={"persist", "remove"})
     * @OrderBy({"position" = "ASC"})
     **/
    protected $controls;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getControls()
    {
        return $this->controls;
    }

    /**
     * @param mixed $controls
     */
    public function setControls($controls)
    {
        $this->controls = $controls;
    }


    public function __construct()
    {
        $this->controls = new ArrayCollection();
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




}