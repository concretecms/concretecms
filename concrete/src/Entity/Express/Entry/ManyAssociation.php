<?php
namespace Concrete\Core\Entity\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressEntityEntryManyAssociations")
 */
class ManyAssociation extends Association
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry", cascade={"persist"})
     * @ORM\JoinTable(name="ExpressEntityEntryManyAssociationSelectedEntries",
     * joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")}
     * )
     */
    protected $selectedEntries;

    /**
     * @return mixed
     */
    public function getSelectedEntries()
    {
        return $this->selectedEntries;
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
