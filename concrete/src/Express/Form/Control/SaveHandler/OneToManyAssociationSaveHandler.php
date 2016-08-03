<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Symfony\Component\HttpFoundation\Request;

class OneToManyAssociationSaveHandler extends ManyAssociationSaveHandler
{

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        $associatedEntries = $this->getAssociatedEntriesFromRequest($control, $request);
        if (count($associatedEntries)) {
            $this->applier->associateOneToMany($control->getAssociation(), $entry, $associatedEntries);
        } else {
            $this->applier->removeAssociation($control->getAssociation(), $entry);
        }
    }



}
