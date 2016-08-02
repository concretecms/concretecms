<?php
namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;
use Doctrine\ORM\EntityManagerInterface;

class Applier
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function associateOne(Association $association, Entry $entry, Entry $associatedEntry)
    {
        // First create the owning entry association
        $oneAssociation = $entry->getAssociation($association->getTargetPropertyName());
        if (!is_object($oneAssociation)) {
            $oneAssociation = new Entry\OneAssociation();
            $oneAssociation->setAssociation($association);
            $oneAssociation->setEntry($entry);
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
        if (!$manyAssociation->getSelectedEntries()->contains($entry)) {
            $manyAssociation->getSelectedEntries()->add($entry);
        }
        $this->entityManager->persist($manyAssociation);
        $this->entityManager->flush();
    }

    public function associateMany(Association $association, Entry $entry, $associatedEntries)
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

        foreach($associatedEntries as $associatedEntry) {
            $oneAssociation = $associatedEntry->getAssociation($association->getInversedByPropertyName());
            if (!is_object($oneAssociation)) {
                $inversedAssocation = $this->entityManager->getRepository(
                    'Concrete\Core\Entity\Express\Association')
                    ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);
                $oneAssociation = new Entry\OneAssociation();
                $oneAssociation->setAssociation($inversedAssocation);
            }
            $oneAssociation->setEntry($associatedEntry);
            $oneAssociation->setSelectedEntry($entry);
            $this->entityManager->persist($oneAssociation);
        }

        $this->entityManager->flush();
    }

    public function removeAssociation(Association $association, Entry $entry)
    {
        $entryAssociation = $entry->getAssociation($association);
        if ($entryAssociation) {
            $inversedAssocation = $this->entityManager->getRepository(
                'Concrete\Core\Entity\Express\Association')
                ->findOneBy(['target_property_name' => $association->getInversedByPropertyName()]);

            $associatedEntries = $entryAssociation->getSelectedEntries();
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
