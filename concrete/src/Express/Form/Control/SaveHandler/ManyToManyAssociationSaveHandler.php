<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Symfony\Component\HttpFoundation\Request;

class ManyToManyAssociationSaveHandler extends ManyAssociationSaveHandler
{

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        if (!$control instanceof AssociationControl) {
            throw new \InvalidArgumentException(t('The control must be an instance of %s.', AssociationControl::class));
        }
        $associatedEntries = $this->getAssociatedEntriesFromRequest($control, $request);
        if (count($associatedEntries)) {
            $this->applier->associateManyToMany($control->getAssociation(), $entry, $associatedEntries);
        } else {
            $this->applier->removeAssociation($control->getAssociation(), $entry);
        }

    }


}
