<?php

namespace Concrete\Core\Entity\Express\Control;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetAssociationControls")
 */
class AssociationControl extends Control
{
    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Association"}))
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



    


}