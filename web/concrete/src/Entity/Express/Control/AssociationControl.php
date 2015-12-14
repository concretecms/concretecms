<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\Form\Control\AssociationControlRenderer;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetAssociationControls")
 */
class AssociationControl extends Control
{
    /**
     * @var \Concrete\Core\Entity\Express\Association
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Association", inversedBy="controls")
     */
    protected $association;

    /**
     * @return mixed
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }


    public function getFormRenderer()
    {
        return new AssociationControlRenderer();
    }

    public function getControlLabel()
    {
        return $this->getAssociation()->getTargetEntity()->getName();
    }

    public function getType()
    {
        return 'association';
    }



}