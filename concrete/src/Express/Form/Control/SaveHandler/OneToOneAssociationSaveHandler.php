<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Symfony\Component\HttpFoundation\Request;

class OneToOneAssociationSaveHandler extends OneAssociationSaveHandler
{

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        if (!$control instanceof AssociationControl) {
            throw new \InvalidArgumentException(t('The control must be an instance of %s.', AssociationControl::class));
        }
        $target = $control->getAssociation()->getTargetEntity();
        $associatedEntry = $this->getAssociatedEntryFromRequest($control, $request);
        if (is_object($associatedEntry) && $associatedEntry->getEntity()->getID() == $target->getID()) {
            $this->applier->associateOneToOne($control->getAssociation(), $entry, $associatedEntry);
        } else {
            $this->applier->removeAssociation($control->getAssociation(), $entry);
        }
    }


}
