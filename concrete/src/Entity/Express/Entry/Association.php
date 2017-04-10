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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID")
     */
    protected $entry;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Association")
     */
    protected $association;

    /**
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry", cascade={"persist"}, inversedBy="containing_associations")
     * @ORM\JoinTable(name="ExpressEntityAssociationSelectedEntries",
     * joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")  }
     * )
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
        if ($this->sorted) {
            return $this->selectedEntries;
        }

        $this->sorted = true;

        // I would use criteria for this but once again Doctrine fails
        if ($this->getAssociation()->getTargetEntity()->supportsCustomDisplayOrder()) {
            $entries = $this->getSelectedEntriesCollection()->toArray();
            usort($entries, function($a, $b) {
                return $a->getEntryDisplayOrder() - $b->getEntryDisplayOrder();
            });

            $this->setSelectedEntries(new ArrayCollection($entries));
        }

        return $this->getSelectedEntriesCollection();
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

    public function removeSelectedEntry(Entry $entry)
    {
        foreach($this->getSelectedEntries() as $selectedEntry) {
            if ($selectedEntry->getId() == $entry->getID()) {
                $this->selectedEntries->removeElement($selectedEntry);
            }
        }
    }


}
