<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\Form\Control\Form\AssociationControlFormRenderer;
use Concrete\Core\Express\Form\Control\View\AssociationControlViewRenderer;
use Concrete\Core\Express\BaseEntity;

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


    public function getFormRenderer(BaseEntity $entity = null)
    {
        return new AssociationControlFormRenderer($entity);
    }

    public function getViewRenderer(BaseEntity $entity)
    {
        return new AssociationControlViewRenderer($entity);
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