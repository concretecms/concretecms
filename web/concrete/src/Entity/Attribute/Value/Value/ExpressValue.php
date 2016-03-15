<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressAttributeValues")
 */
class ExpressValue extends Value
{
    /**
     * @ORM\ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry", inversedBy="values", cascade={"persist"})
     * @ORM\JoinTable(name="ExpressAttributeValueSelectedEntries",
     * joinColumns={@ORM\JoinColumn(name="avID", referencedColumnName="avID")},
     * inverseJoinColumns={@ORM\JoinColumn(name="exEntryID", referencedColumnName="exEntryID")}
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
