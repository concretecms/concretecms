<?php
namespace Concrete\Core\Entity\Express\Entry;

/**
 * @Entity
 * @Table(name="ExpressEntityEntryOneAssociations")
 */
class OneAssociation extends Association
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")
     */
    protected $selected_entry;

    /**
     * @return mixed
     */
    public function getSelectedEntry()
    {
        return $this->selected_entry;
    }

    public function getSelectedEntries()
    {
        return array($this->getSelectedEntry());
    }

    /**
     * @param mixed $selected_entry
     */
    public function setSelectedEntry($selected_entry)
    {
        $this->selected_entry = $selected_entry;
    }




}
