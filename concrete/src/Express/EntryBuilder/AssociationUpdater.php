<?php
namespace Concrete\Core\Express\EntryBuilder;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Association\Applier;

class AssociationUpdater
{

    protected $applier;
    protected $entry;

    public function __construct(Applier $applier, Entry $entry)
    {
        $this->applier = $applier;
        $this->entry = $entry;
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            $method = preg_replace('/(?!^)[[:upper:]]/', '_\0', $method);
            $method = strtolower($method);
            $identifier = substr($method, 4);
            $this->associate($identifier, $arguments[0]);
        }
        if (substr($method, 0, 6) == 'remove') {
            $method = preg_replace('/(?!^)[[:upper:]]/', '_\0', $method);
            $method = strtolower($method);
            $identifier = substr($method, 7);
            $entriesToRemove = (array) $arguments[0];
            $newList = [];
            $entryIdsToRemove = [];
            foreach($entriesToRemove as $entryToRemove) {
                $entryIdsToRemove[] = $entryToRemove->getID();
            }

            // Now we get the list of currently associated items
            $entryAssociation = $this->entry->getAssociation($identifier);
            $selectedEntries = $entryAssociation->getSelectedEntries();
            foreach($selectedEntries as $selectedEntry) {
                if (!in_array($selectedEntry->getId(), $entryIdsToRemove)) {
                    $newList[] = $selectedEntry;
                }
            }
            $this->associate($identifier, $newList);
        }

        return $this;
    }

    public function associate($associationHandle, $input)
    {
        $association = $this->entry->getEntity()->getAssociation($associationHandle);
        $this->applier->associate($association, $this->entry, $input);
    }


}
