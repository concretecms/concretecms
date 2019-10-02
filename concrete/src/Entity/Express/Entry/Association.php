<?php
namespace Concrete\Core\Entity\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="ExpressEntityEntryAssociations")
 */
abstract class Association
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry", inversedBy="associations")
     * @ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID")
     */
    protected $entry;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Association", inversedBy="entry")
     */
    protected $association;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Express\Entry\AssociationEntry", mappedBy="association", cascade={"all"})
     */
    protected $selectedEntries;

    /**
     * @var bool A boolean to track whether the selected entries are sorted
     */
    protected $sorted;

    /**
     * @return \Concrete\Core\Entity\Express\Association
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    public function getSelectedEntriesCollection()
    {
        return $this->selectedEntries;
    }

    /**
     * @return mixed
     */
    public function getSelectedEntries()
    {

        // I would use criteria for this but once again Doctrine fails
        if ($this->getAssociation()->getTargetEntity()->supportsCustomDisplayOrder()) {
            $entries = [];
            foreach ($this->getSelectedEntriesCollection() as $associationEntry) {
                $entries[] = $associationEntry->getEntry();
            }

            usort($entries, function ($a, $b) {
                return $a->getEntryDisplayOrder() - $b->getEntryDisplayOrder();
            });

            $sortedEntries = new ArrayCollection($entries);
        } else {

            // we rely on the display order that is set at the entry\association level
            $entries = [];
            foreach ($this->getSelectedEntriesCollection() as $associationEntry) {
                $entries[] = $associationEntry;
            }

            usort($entries, function ($a, $b) {
                return $a->getDisplayOrder() - $b->getDisplayOrder();
            });

            $sortedEntries = [];
            foreach($entries as $associationEntry) {
                $sortedEntries[] = $associationEntry->getEntry();
            }
            $sortedEntries = new ArrayCollection($sortedEntries);

        }

        return $sortedEntries;
    }

    /**
     * @param mixed $selectedEntries
     */
    public function setSelectedEntries($selectedEntries)
    {
        $this->selectedEntries = $selectedEntries;
    }

    public function __construct()
    {
        $this->selectedEntries = new ArrayCollection();
    }

    public function getAssociationEntry(Entry $entry)
    {
        foreach($this->getSelectedEntriesCollection() as $associatedEntry) {
            if ($associatedEntry->getEntry()->getId() == $entry->getId()) {
                return $associatedEntry;
            }
        }
    }

}
