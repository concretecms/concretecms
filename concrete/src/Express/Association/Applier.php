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
            $oneAssociation->setSelectedEntries(new ArrayCollection());
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $inversedAssociation = $this->getInverseAssociation($association);
                $manyAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($manyAssociation) {
                    $collection = $manyAssociation->getSelectedEntriesCollection();
                    if ($collection) {
                        $collection->removeElement($entry);
                        $manyAssociation->setSelectedEntries(new ArrayCollection($collection->getValues()));
                        $this->entityManager->persist($manyAssociation);
                    }
                }
            }
        }

        $oneAssociation->getSelectedEntries()->add($associatedEntry);
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

        $collection->add($entry);
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
                $oneEntryAssociation = $oneEntry->getEntryAssociation($association);
                if ($oneEntryAssociation) {
                    $oneEntryAssociation->getSelectedEntriesCollection()->removeElement($selectedEntry);
                    $this->entityManager->persist($oneEntryAssociation);
                }
                $this->entityManager->remove($oneAssociation);
            }
        }

        $this->entityManager->flush();

        $manyAssociation->setSelectedEntries($associatedEntries);
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
                    $oneAssociation->setSelectedEntry($entry);
                    $this->entityManager->persist($oneAssociation);
                } else {
                    foreach($collection as $result) {
                        if ($result->getId() == $entry->getId()) {
                            // The result is already selected, so we don't reselect it.
                            continue;
                        } else {
                            // We are currently set to a different result. So we need to delete this association and
                            // Set it to this one.
                            $oneAssociation->clearSelectedEntry();
                            $this->entityManager->persist($oneAssociation);
                        }
                    }
                }
            } else {
                if (count($collection) > 0) {
                    foreach($collection as $result) {
                        if ($result->getId() == $entry->getId()) {
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
        }
        $manyAssociation->setSelectedEntries($associatedEntries);
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

            $collection = $manyAssociation->getSelectedEntriesCollection();

            if (in_array($possibleResult, $associatedEntries)) {
                // If the item appears in the request (meaning we want it to be selected):
                if (!$collection->contains($entry)) {
                    $collection->add($entry);
                }
                $newCollection = new ArrayCollection($collection->getValues());
                $manyAssociation->setSelectedEntries($newCollection);
                $this->entityManager->persist($manyAssociation);
            } else {
                if (count($collection) > 0) {
                    foreach($collection as $result) {
                        if ($result->getId() == $entry->getId()) {
                            $collection->removeElement($entry);
                            $manyAssociation->setSelectedEntries(new ArrayCollection($collection->getValues()));
                            $this->entityManager->persist($manyAssociation);
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
            $oneAssociation->setSelectedEntries(new ArrayCollection());
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $inversedAssociation = $this->getInverseAssociation($association);
                $otherOneAssociation = $selectedEntry->getEntryAssociation($inversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociation->clearSelectedEntry();
                    $this->entityManager->persist($otherOneAssociation);
                }
            }
        }

        $oneAssociation->setSelectedEntry($associatedEntry);
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
            $oneAssociation->clearSelectedEntry();
            if ($selectedEntry) {
                // let's loop through the inverse association and remove this one from it.
                $otherInversedAssociation = $this->getInverseAssociation($inversedAssociation);
                $otherOneAssociation = $selectedEntry->getEntryAssociation($otherInversedAssociation);
                if ($otherOneAssociation) {
                    $otherOneAssociation->clearSelectedEntry();
                    $this->entityManager->persist($otherOneAssociation);
                }
            }

            $this->entityManager->persist($oneAssociation);
            $this->entityManager->flush();
        }

        $oneAssociation->setSelectedEntry($entry);
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
                $associatedEntryAssociation = $associatedEntry->getEntryAssociation($inversedAssociation);
                if (is_object($associatedEntryAssociation)) {
                    $associatedEntryAssociation->removeSelectedEntry($entry);
                    $this->entityManager->persist($associatedEntryAssociation);
                }
            }

            $this->entityManager->remove($entryAssociation);
            $this->entityManager->flush();
        }
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
