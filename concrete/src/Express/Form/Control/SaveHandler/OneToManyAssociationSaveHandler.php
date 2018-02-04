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
        $association = $control->getAssociation();
        if ($association->isOwningAssociation()) {
            // If the owned entity supports display order, we save display order here. Otherwise we return.
            if ($association->getTargetEntity()->supportsCustomDisplayOrder()) {
                $i = 0;
                foreach($associatedEntries as $entry) {
                    $entry->setEntryDisplayOrder($i);
                    $this->entityManager->persist($entry);
                    $i++;
                }
                $this->entityManager->flush();
            }
            return;
        }

        if (count($associatedEntries)) {
            $this->applier->associateOneToMany($control->getAssociation(), $entry, $associatedEntries);
        } else {
            $this->applier->removeAssociation($control->getAssociation(), $entry);
        }
    }



}
