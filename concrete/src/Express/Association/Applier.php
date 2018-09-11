<?php
namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Express\EntryList;
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
        // First create the owning entry association
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }

        foreach($associatedEntries as $selectedEntry) {
            $inversedAssociation = $this->getInverseAssociation($association);
            $oneAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
            if ($oneAssociation) {
                // Let's see if THAT entry relates back to this.
                $oneEntry = $oneAssociation->getSelectedEntry();
                if ($oneEntry) {
                    $oneEntryAssociation = $oneEntry->getEntryAssociation($association);
                    if ($oneEntryAssociation) {
                        foreach($oneEntryAssociation->getSelectedEntriesCollection() as $oneEntryAssociationEntry) {
                            if ($oneEntryAssociationEntry->getEntry()->getId() == $selectedEntry->getId()) {
                                $this->entityManager->remove($oneEntryAssociationEntry);
                            }
                        }
                        $this->entityManager->flush();
                    }
                }
                $this->entityManager->remove($oneAssociation);
            }
        }

        $this->entityManager->flush();

        foreach($manyAssociation->getSelectedEntriesCollection() as $manyAssociationSelectedEntry) {
            $this->entityManager->remove($manyAssociationSelectedEntry);
        }
        $this->entityManager->flush();

        $associationAssociatedEntries = [];
        $displayOrder = 0;
        foreach($associatedEntries as $associatedEntry) {
            $associationEntry = new Entry\AssociationEntry();
            $associationEntry->setEntry($associatedEntry);
            $associationEntry->setAssociation($manyAssociation);
            $associationEntry->setDisplayOrder($displayOrder);
            $displayOrder++;
            $associationAssociatedEntries[] = $associationEntry;
        }

        $manyAssociation->setSelectedEntries($associationAssociatedEntries);
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();

        // Now, we go to the inverse side, and we get all possible entries.
        $entity = $association->getTargetEntity();
        $list = new EntryList($entity);
        $list->ignorePermissions();
        $possibleResults = $list->getResults();
        foreach($possibleResults as $possibleResult) {
            $inversedAssociation = $this->getInverseAssociation($association);
            $oneAssociation = $possibleResult->getEntryAssociation($inversedAssociation);
            if (!is_object($oneAssociation)) {
                $oneAssociation = new Entry\OneAssociation();
                $oneAssociation->setAssociation($inversedAssociation);
                $oneAssociation->setEntry($possibleResult);
            }

            $collection = $oneAssociation->getSelectedEntriesCollection();

            if (in_array($possibleResult, $associatedEntries)) {
                // If the item appears in the request (meaning we want it to be selected):
                if (count($collection) == 0) {
                    // Nothing is currently selected, so we have to add this one.
                    $oneAssociationEntry = new Entry\AssociationEntry();
                    $oneAssociationEntry->setEntry($entry);
                    $oneAssociationEntry->setAssociation($oneAssociation);
                    $oneAssociation->setSelectedEntry($oneAssociationEntry);
                    $this->entityManager->persist($oneAssociation);
                } else {
                    foreach($collection as $result) {
                        if ($result->getId() == $entry->getId()) {
                            // The result is already selected, so we don't reselect it.
                            continue;
                        } else {
                            // We are currently set to a different result. So we need to delete this association and
                            // Set it to this one.
                            $oneAssociationCollection = $oneAssociation->getSelectedEntriesCollection();
                            foreach($oneAssociationCollection as $oneAssociationEntry) {
                                $this->entityManager->remove($oneAssociationEntry);
                            }
                        }
                    }
                }
            } else {
                if (count($collection) > 0) {
                    foreach($collection as $result) {
                        if ($result->getEntry()->getId() == $entry->getId()) {
                            // The result is currently in the collection, so let's remove the association entirely..
                            $this->entityManager->remove($oneAssociation);
                        }
                    }
                }
            }
        }

        $this->entityManager->flush();
    }

    public function associateManyToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // First create the owning entry association
        $manyAssociation = $entry->getEntryAssociation($association);
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
            $displayOrder = 0;
        } else {
            $displayOrder = count($manyAssociation->getSelectedEntriesCollection());
        }
        $associatedAssociationEntries = [];
        foreach($associatedEntries as $associatedEntry) {
            $associatedAssociationEntry = new Entry\AssociationEntry();
            $associatedAssociationEntry->setEntry($associatedEntry);
            $associatedAssociationEntry->setAssociation($manyAssociation);
            $associatedAssociationEntry->setDisplayOrder($displayOrder);
            $displayOrder++;
            $associatedAssociationEntries[] = $associatedAssociationEntry;
        }

        $manyAssociation->setSelectedEntries($associatedAssociationEntries);
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();

        // Now, we go to the inverse side, and we get all possible entries.
        $entity = $association->getTargetEntity();
        $list = new EntryList($entity);
        $list->ignorePermissions();
        $possibleResults = $list->getResults();
        foreach($possibleResults as $possibleResult) {
            $inversedAssociation = $this->getInverseAssociation($association);
            $manyAssociation = $possibleResult->getEntryAssociation($inversedAssociation);
            if (!is_object($manyAssociation)) {
                $manyAssociation = new Entry\ManyAssociation();
                $manyAssociation->setAssociation($inversedAssociation);
                $manyAssociation->setEntry($possibleResult);
            }


            if (in_array($possibleResult, $associatedEntries)) {
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
                $inversedAssociation = $this->getInverseAssociation($association);
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

        $inversedAssociation = $this->getInverseAssociation($association);
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
