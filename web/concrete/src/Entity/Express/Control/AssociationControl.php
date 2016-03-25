<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\Form\AssociationControlFormRenderer;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\AssociationControlSaveHandler;
use Concrete\Core\Express\Form\Control\View\AssociationControlViewRenderer;
use Concrete\Core\Entity\Express\Entity;

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
     * @Column(type="string", nullable=true)
     */
    protected $association_entity_label_mask;

    /**
     * @return mixed
     */
    public function getAssociationEntityLabelMask()
    {
        return $this->association_entity_label_mask;
    }

    /**
     * @param mixed $association_entity_label_mask
     */
    public function setAssociationEntityLabelMask($association_entity_label_mask)
    {
        $this->association_entity_label_mask = $association_entity_label_mask;
    }

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

    public function getFormControlRenderer(Entry $entry = null)
    {
        return new AssociationControlFormRenderer($entry);
    }

    public function getViewControlRenderer(Entry $entry)
    {
        return new AssociationControlViewRenderer($entry);
    }

    public function getControlSaveHandler()
    {
        return new AssociationControlSaveHandler();
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
