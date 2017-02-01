<?php
namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Express\EntryList;
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
        $oneAssociation = $entry->getAssociation($association->getTargetPropertyName());
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // We clear out the one association
            $oneAssociation->getSelectedEntries()->remove(0);
        }

        $oneAssociation->setSelectedEntry($associatedEntry);
        $this->entityManager->persist($oneAssociation);

        // Now, on the associated entry, populate a Many association so we kee this up to date.
        // Let's see if there's an existing one we can use.
        $manyAssociation = $associatedEntry->getAssociation($association->getInversedByPropertyName());
        if (!is_object($manyAssociation)) {
            $inversedAssocation = $this->entityManager->getRepository(
                'Concrete\Core\Entity\Express\Association')
                ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($inversedAssocation);
        }
        $manyAssociation->setEntry($associatedEntry);
        if (!$manyAssociation->getSelectedEntriesCollection()->contains($entry)) {
            $manyAssociation->getSelectedEntriesCollection()->add($entry);
        }
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();
    }

    public function associateOneToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // First create the owning entry association
        $manyAssociation = $entry->getAssociation($association->getTargetPropertyName());
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }
        $manyAssociation->setSelectedEntries($associatedEntries);
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();

        // Now, we go to the inverse side, and we get all possible entries. We loop through them to see whether they're in the associated entries array
        $entity = $association->getTargetEntity();
        $list = new EntryList($entity);
        $list->ignorePermissions();
        $possibleResults = $list->getResults();
        foreach($possibleResults as $possibleResult) {
            $oneAssociation = $possibleResult->getAssociation($association->getInversedByPropertyName());
            if (!is_object($oneAssociation)) {
                $inversedAssocation = $this->entityManager->getRepository(
                    'Concrete\Core\Entity\Express\Association')
                    ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);
                $oneAssociation = new Entry\OneAssociation();
                $oneAssociation->setAssociation($inversedAssocation);
            } else {
                // We clear out the one association
                $oneAssociation->getSelectedEntriesCollection()->remove(0);
            }
            $oneAssociation->setEntry($possibleResult);
            if (in_array($possibleResult, $associatedEntries)) {
                $oneAssociation->setSelectedEntry($entry);
                $this->entityManager->persist($oneAssociation);
            } else {
                // It's not in the array, which means we have to remove it from
                // the inverse one assocation
                $this->entityManager->remove($oneAssociation);
            }
        }

        $this->entityManager->flush();
    }

    public function associateManyToMany(Association $association, Entry $entry, $associatedEntries)
    {
        // First create the owning entry association
        $manyAssociation = $entry->getAssociation($association->getTargetPropertyName());
        if (!is_object($manyAssociation)) {
            $manyAssociation = new Entry\ManyAssociation();
            $manyAssociation->setAssociation($association);
            $manyAssociation->setEntry($entry);
        }
        $manyAssociation->setSelectedEntries($associatedEntries);
        $this->entityManager->persist($manyAssociation);

        // Now, on the associated entry, populate a Many association so we kee this up to date.
        // Let's see if there's an existing one we can use.
        foreach($associatedEntries as $associatedEntry) {
            $manyAssociation = $associatedEntry->getAssociation($association->getInversedByPropertyName());
            if (!is_object($manyAssociation)) {
                $inversedAssocation = $this->entityManager->getRepository(
                    'Concrete\Core\Entity\Express\Association')
                    ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);
                $manyAssociation = new Entry\ManyAssociation();
                $manyAssociation->setAssociation($inversedAssocation);
            }
            $manyAssociation->setEntry($associatedEntry);
            if (!$manyAssociation->getSelectedEntriesCollection()->contains($entry)) {
                $manyAssociation->getSelectedEntriesCollection()->add($entry);
            }
            $this->entityManager->persist($manyAssociation);
        }
        $this->entityManager->flush();
    }

    public function associateOneToOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        // First create the owning entry association
        $oneAssociation = $entry->getAssociation($association->getTargetPropertyName());
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
        } else {
            // We clear out the one association
            $oneAssociation->getSelectedEntriesCollection()->remove(0);
        }

        $oneAssociation->setSelectedEntry($associatedEntry);
        $this->entityManager->persist($oneAssociation);

        $oneAssociation = $associatedEntry->getAssociation($association->getInversedByPropertyName());
        if (!is_object($oneAssociation)) {
            $inversedAssocation = $this->entityManager->getRepository(
                'Concrete\Core\Entity\Express\Association')
                ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($inversedAssocation);
        } else {
            // We clear out the one association
            $oneAssociation->getSelectedEntriesCollection()->remove(0);
        }
        $oneAssociation->setEntry($associatedEntry);
        $oneAssociation->setSelectedEntry($entry);

        $this->entityManager->persist($oneAssociation);
        $this->entityManager->flush();
    }

    public function removeAssociation(Association $association, Entry $entry)
    {
        $entryAssociation = $entry->getAssociation($association);
        if ($entryAssociation) {
            $inversedAssocation = $this->entityManager->getRepository(
                'Concrete\Core\Entity\Express\Association')
                ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);

            $associatedEntries = $entryAssociation->getSelectedEntriesCollection();
            foreach($associatedEntries as $associatedEntry) {
                $associatedEntryAssociation = $associatedEntry->getAssociation($inversedAssocation);
                if (is_object($associatedEntryAssociation)) {
                    $associatedEntryAssociation->removeSelectedEntry($entry);
                    $this->entityManager->persist($associatedEntryAssociation);
                }
            }

            $this->entityManager->remove($entryAssociation);
            $this->entityManager->flush();
        }
    }
}
