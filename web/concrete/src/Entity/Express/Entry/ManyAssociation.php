<?php
namespace Concrete\Core\Entity\Express\Entry;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ExpressEntityEntryManyAssociations")
 */
class ManyAssociation extends Association
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ManyToMany(targetEntity="\Concrete\Core\Entity\Express\Entry", cascade={"persist"})
     * @JoinTable(name="ExpressEntityEntryManyAssociationSelectedEntries",
     * joinColumns={@JoinColumn(name="id", referencedColumnName="id")},
     * inverseJoinColumns={@JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")}
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


}
