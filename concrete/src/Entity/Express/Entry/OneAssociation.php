<?php
namespace Concrete\Core\Entity\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressEntityEntryOneAssociations")
 */
class OneAssociation extends Association
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry")
     * @ORM\JoinColumn(name="exSelectedEntryID", referencedColumnName="exEntryID")
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

    public function removeSelectedEntry(Entry $entry)
    {
        if ($entry->getID() == $this->selected_entry->getID()) {
            $this->selected_entry = null;
        }
    }


}
