<?php
namespace Concrete\Core\Entity\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OneAssociation extends Association
{

    /**
     * @return mixed
     */
    public function getSelectedEntry()
    {
        return $this->getSelectedEntries()[0];
    }

    /**
     * @param mixed $selected_entry
     */
    public function setSelectedEntry($selected_entry)
    {
        $this->selectedEntries = new ArrayCollection();
        $this->selectedEntries->add($selected_entry);
    }

    public function clearSelectedEntry()
    {
        $this->selectedEntries = new ArrayCollection();
    }


}
