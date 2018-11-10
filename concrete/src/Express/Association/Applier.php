<?php
namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class Applier
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * A generic associate method that can be run from elsewhere. Determines the appropriate associate* method
     * to run.
     * @param Association $association
     * @param Entry $entry
     * @param $input
     */
    public function associate(Association $association, Entry $entry, $input)
    {
        if ($association instanceof ManyToOneAssociation) {
            return $this->associateManyToOne($association, $entry, $input);
        }
        if ($association instanceof OneToOneAssociation) {
            return $this->associateOneToOne($association, $entry, $input);
        }
        if ($association instanceof OneToManyAssociation) {
            return $this->associateOneToMany($association, $entry, $input);
        }
        if ($association instanceof ManyToManyAssociation) {
            return $this->associateManyToMany($association, $entry, $input);
        }
    }

    public function associateManyToOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        // First create the owning entry association
        $oneAssociation = $entry->getEntryAssociation($association);
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // We clear out the one association
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            foreach($oneAssociation->getSelectedEntriesCollection() as $selectedAssociationEntry) {
                $this->entityManager->remove($selectedAssociationEntry);
            }
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $inversedAssociation = $this->getInverseAssociation($association);
                $manyAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($manyAssociation) {
                    foreach($manyAssociation->getSelectedEntriesCollection() as $selectedAssociationEntry) {
                        if ($selectedAssociationEntry->getEntry()->getId() == $entry->getId()) {
                            $this->entityManager->remove($selectedAssociationEntry);
                        }
                    }
                    $this->entityManager->flush();
                }
            }
        }

        $associatedAssociationEntry = new Entry\AssociationEntry();
        $associatedAssociationEntry->setEntry($associatedEntry);
        $associatedAssociationEntry->setAssociation($oneAssociation);
        $this->entityManager->persist($associatedAssociationEntry);

        $oneAssociation->getSelectedEntriesCollection()->add($associatedAssociationEntry);
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();

        // Now, on the associated entry, populate a Many association so we kee this up to date.
        // Let's see if there's an existing one we can use.
        $inversedAssociation = $this->getInverseAssociation($association);
        $manyAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($inversedAssociation);
        }
        $manyAssociation->setEntry($associatedEntry);

        // Now lets loop through all the entries
        $collection = $manyAssociation->getSelectedEntriesCollection();
        if (!$collection) {
            $collection = new ArrayCollection();
        }
        $displayOrder = count($collection);

        $associationEntry = new Entry\AssociationEntry();
        $associationEntry->setEntry($entry);
        $associationEntry->setDisplayOrder($displayOrder);
        $associationEntry->setAssociation($manyAssociation);

        $collection->add($associationEntry);
        $manyAssociation->setSelectedEntries($collection);

        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();
    }

    public function associateOneToMany(Association $association, Entry $entry, $associatedEntries)
    {

        // Let's first get the inverse association, because we're going to need. it.
        $inversedAssociation = $this->getInverseAssociation($association);

        // Let's get an array of entry IDs so we can use in_array() easily.
        $associatedEntryIDs = [];
        foreach($associatedEntries as $associatedEntry) {
            $associatedEntryIDs[] = $associatedEntry->getId();
        }

        // Next create the owning entry association
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }
        $this->entityManager->persist($manyAssociation);

        $existingSelectedEntryIDs = [];
        $displayOrder = 0;
        foreach ($manyAssociation->getSelectedEntriesCollection() as $manyAssociationSelectedEntry) {
            $manyAssociationSelectedEntryID = $manyAssociationSelectedEntry->getEntry()->getID();
            $existingSelectedEntryIDs[] = $manyAssociationSelectedEntryID;
            if (!in_array($manyAssociationSelectedEntryID, $associatedEntryIDs)) {
                // Since this is not passed in associatedEntries, but it is currently present, we need to remove it.
                $this->entityManager->remove($manyAssociationSelectedEntry);
            } else {
                $displayOrder = $manyAssociationSelectedEntry->getDisplayOrder();
            }
        }

        // Have to flush here, because of the remove() statement above. If we wait it doesn't get applied.
        $this->entityManager->flush();

        // Prepare to add new entries.
        if ($displayOrder > 0) {
            $displayOrder++;
        }

        // Now, let's add entries to the owning side that don't currently exist in the owning side.
        foreach($associatedEntries as $associatedEntry) {
            if (!in_array($associatedEntry->getId(), $existingSelectedEntryIDs)) {
                $associationEntry = new Entry\AssociationEntry();
                $associationEntry->setEntry($associatedEntry);
                $associationEntry->setAssociation($manyAssociation);
                $associationEntry->setDisplayOrder($displayOrder);
                $this->entityManager->persist($associationEntry);
                $manyAssociation->getSelectedEntriesCollection()->add($associationEntry);
                $displayOrder++;

                // Let's make sure that the entry we're attempt to add to this entry, isn't already
                // bound to another entry. If it is, we need to remove that inverse.
                $associationEntryOneAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);
                if ($associationEntryOneAssociation) {
                    // Let's see if THAT entry relates back to this.
                    $oneEntry = $associationEntryOneAssociation->getSelectedEntry();
                    if ($oneEntry) {
                        $oneEntryManyAssociation = $oneEntry->getEntryAssociation($association);
                        if ($oneEntryManyAssociation) {
                            foreach($oneEntryManyAssociation->getSelectedEntriesCollection() as $oneEntryAssociationEntry) {
                                if ($oneEntryAssociationEntry->getEntry()->getId() == $associatedEntry->getId()) {
                                    $this->entityManager->remove($oneEntryAssociationEntry);
                                }
                            }
                            $this->entityManager->flush();
                        }
                    }
                }
            }
        }

        $this->entityManager->flush();


        // Lets clear out the existing one associations.
        // Let's combine our arrays and filter out duplicates.
        $relevantInverseEntryIDs = array_unique(array_merge($associatedEntryIDs, $existingSelectedEntryIDs));
        foreach($relevantInverseEntryIDs as $entryID) {
            $inverseEntry = $this->entityManager->find(Entry::class, $entryID);
            $oneAssociation = $inverseEntry->getEntryAssociation($inversedAssociation);
            if ($oneAssociation) {
                $this->entityManager->remove($oneAssociation);
            }
        }

        $this->entityManager->flush();

        foreach($relevantInverseEntryIDs as $entryID) {
            $inverseEntry = $this->entityManager->find(Entry::class, $entryID);
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($inversedAssociation);
            $oneAssociation->setEntry($inverseEntry);
            $this->entityManager->persist($oneAssociation);

            if (in_array($entryID, $associatedEntryIDs)) {
                // Select $entry for the toOne side of the association.
                $oneAssociationEntry = new Entry\AssociationEntry();
                $oneAssociationEntry->setEntry($entry);
                $oneAssociationEntry->setAssociation($oneAssociation);
                $oneAssociation->setSelectedEntry($oneAssociationEntry);
                $this->entityManager->persist($oneAssociation);
            }
        }
        $this->entityManager->flush();
    }

    public function associateManyToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // Let's first get the inverse association, because we're going to need. it.
        $inversedAssociation = $this->getInverseAssociation($association);

        // Let's get an array of entry IDs so we can use in_array() easily.
        $associatedEntryIDs = [];
        foreach($associatedEntries as $associatedEntry) {
            $associatedEntryIDs[] = $associatedEntry->getId();
        }

        // Next create the owning entry association
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }
        $this->entityManager->persist($manyAssociation);

        $existingSelectedEntryIDs = [];
        $displayOrder = 0;
        foreach ($manyAssociation->getSelectedEntriesCollection() as $manyAssociationSelectedEntry) {
            $manyAssociationSelectedEntryID = $manyAssociationSelectedEntry->getEntry()->getID();
            $existingSelectedEntryIDs[] = $manyAssociationSelectedEntryID;
            if (!in_array($manyAssociationSelectedEntryID, $associatedEntryIDs)) {
                // Since this is not passed in associatedEntries, but it is currently present, we need to remove it.
                $this->entityManager->remove($manyAssociationSelectedEntry);
            } else {
                $displayOrder = $manyAssociationSelectedEntry->getDisplayOrder();
            }
        }

        // Have to flush here, because of the remove() statement above. If we wait it doesn't get applied.
        $this->entityManager->flush();

        // Prepare to add new entries.
        if ($displayOrder > 0) {
            $displayOrder++;
        }

        // Now, let's add entries to the owning side that don't currently exist in the owning side.
        foreach($associatedEntries as $associatedEntry) {
            if (!in_array($associatedEntry->getId(), $existingSelectedEntryIDs)) {
                $associationEntry = new Entry\AssociationEntry();
                $associationEntry->setEntry($associatedEntry);
                $associationEntry->setAssociation($manyAssociation);
                $associationEntry->setDisplayOrder($displayOrder);
                $this->entityManager->persist($associationEntry);
                $manyAssociation->getSelectedEntriesCollection()->add($associationEntry);
                $displayOrder++;
            }
        }

        $this->entityManager->flush();

        $relevantInverseEntryIDs = array_unique(array_merge($associatedEntryIDs, $existingSelectedEntryIDs));
        foreach($relevantInverseEntryIDs as $entryID) {
            $inverseEntry = $this->entityManager->find(Entry::class, $entryID);
            $manyAssociation = $inverseEntry->getEntryAssociation($inversedAssociation);
            if (!is_object($manyAssociation)) {
                $manyAssociation = new Entry\ManyAssociation();
                $manyAssociation->setAssociation($inversedAssociation);
                $manyAssociation->setEntry($inverseEntry);
            }


            if (in_array($entryID, $associatedEntryIDs)) {
                $selectedEntries = $manyAssociation->getSelectedEntries();
                // If the item appears in the request (meaning we want it to be selected):
                if (!$selectedEntries->contains($entry)) {
                    $associationEntry = new Entry\AssociationEntry();
                    $associationEntry->setAssociation($manyAssociation);
                    $associationEntry->setEntry($entry);
                    $associationEntry->setDisplayOrder(count($selectedEntries));
                    $manyAssociation->getSelectedEntriesCollection()->add($associationEntry);
                }
                $this->entityManager->persist($manyAssociation);
            } else {
                $selectedEntriesCollection = $manyAssociation->getSelectedEntriesCollection();
                if (count($selectedEntriesCollection) > 0) {
                    foreach($selectedEntriesCollection as $result) {
                        if ($result->getEntry()->getId() == $entry->getId()) {
                            $this->entityManager->remove($result);
                        }
                    }
                }
            }
        }
        $this->entityManager->flush();
    }

    public function associateOneToOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        // Locate the inverse association
        $inversedAssociation = $this->getInverseAssociation($association);

        $oneAssociation = $entry->getEntryAssociation($association);
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // We clear out the one association
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            $oneAssociationCollection = $oneAssociation->getSelectedEntriesCollection();
            foreach($oneAssociationCollection as $oneAssociationEntry) {
                $this->entityManager->remove($oneAssociationEntry);
            }
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $otherOneAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociationCollection = $otherOneAssociation->getSelectedEntriesCollection();
                    foreach($otherOneAssociationCollection as $otherOneAssociationEntry) {
                        $this->entityManager->remove($otherOneAssociationEntry);
                    }
                    $this->entityManager->persist($otherOneAssociation);
                }
            }
        }

        $associationAssociatedEntry = new Entry\AssociationEntry();
        $associationAssociatedEntry->setEntry($associatedEntry);
        $associationAssociatedEntry->setAssociation($oneAssociation);
        $oneAssociation->setSelectedEntry($associationAssociatedEntry);
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();

        $oneAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);

        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($inversedAssociation);
            $oneAssociation->setEntry($associatedEntry);
        } else {
            $selectedEntry = $oneAssociation->getSelectedEntries()->get(0);
            $oneAssociationCollection = $oneAssociation->getSelectedEntriesCollection();
            foreach($oneAssociationCollection as $oneAssociationEntry) {
                $this->entityManager->remove($oneAssociationEntry);
            }
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $otherInversedAssociation = $this->getInverseAssociation($inversedAssociation);
                $otherOneAssociation = $selectedEntry->getEntryAssociation($otherInversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociationCollection = $otherOneAssociation->getSelectedEntriesCollection();
                    foreach($otherOneAssociationCollection as $otherOneAssociationEntry) {
                        $this->entityManager->remove($otherOneAssociationEntry);
                    }
                }
            }
            $this->entityManager->flush();
        }

        $associationAssociatedEntry = new Entry\AssociationEntry();
        $associationAssociatedEntry->setEntry($entry);
        $associationAssociatedEntry->setAssociation($oneAssociation);
        $oneAssociation->setSelectedEntry($associationAssociatedEntry);
        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();

    }

    public function removeAssociation(Association $association, Entry $entry)
    {
        $entryAssociation = $entry->getEntryAssociation($association);
        if ($entryAssociation) {
            $inversedAssociation = $this->getInverseAssociation($association);
            $associatedEntries = $entryAssociation->getSelectedEntriesCollection();
            foreach($associatedEntries as $associatedEntry) {
                $associatedEntryAssociation = $associatedEntry->getEntry()->getEntryAssociation($inversedAssociation);
                if (is_object($associatedEntryAssociation)) {
                    foreach($associatedEntryAssociation->getSelectedEntriesCollection() as $associatedAssociationEntry) {
                        if ($associatedAssociationEntry->getEntry()->getId() == $entry->getId()) {
                            $this->entityManager->remove($associatedAssociationEntry);
                        }
                    }
                }
                $this->entityManager->remove($associatedEntry);
            }

            $this->entityManager->flush();

            $this->entityManager->remove($entryAssociation);
            $this->entityManager->flush();

            if (isset($associatedEntryAssociation)) {
                $this->rescanAssociationDisplayOrder($associatedEntryAssociation);
            }
        }
    }

    private function rescanAssociationDisplayOrder(Entry\Association $association)
    {
        $displayOrder = 0;
        foreach($association->getSelectedEntriesCollection() as $selectedEntry) {
            $selectedEntry->setDisplayOrder($displayOrder);
            $this->entityManager->persist($selectedEntry);
            $displayOrder++;
        }
        $this->entityManager->flush();
    }

    protected function getInverseAssociation(Association $association)
    {
        return $this->entityManager->getRepository(Association::class)
            ->findOneBy([
                'target_property_name' => $association->getInversedByPropertyName(),
                'inversed_by_property_name' => $association->getTargetPropertyName(),
                'target_entity' => $association->getSourceEntity(),
                'source_entity' => $association->getTargetEntity()
            ]);
    }


}
