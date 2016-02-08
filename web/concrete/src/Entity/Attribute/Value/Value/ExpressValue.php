<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ExpressAttributeValues")
 */
class ExpressValue extends Value
{
    /**
     * @ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry", inversedBy="values", cascade={"persist"})
     * @JoinTable(name="ExpressAttributeValueSelectedEntries",
     * joinColumns={@JoinColumn(name="avID", referencedColumnName="avID")},
     * inverseJoinColumns={@JoinColumn(name="exEntryID", referencedColumnName="exEntryID")}
     * )
     */
    protected $selectedEntries;

    public function __construct()
    {
        parent::__construct();
        $this->selectedEntries = new ArrayCollection();
    }

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
}
