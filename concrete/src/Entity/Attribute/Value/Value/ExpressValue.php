<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\Entity\Express\Entry;
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

    public function __toString()
    {
        $str = '';
        $i = 0;
        /**
         * @var $option Entry
         */
        foreach($this->selectedEntries as $option) {
            $str .= $option->getLabel();
            $i++;
            if ($i < count($this->selectedEntries)) {
                $str .= "\n";
            }
        }
        return $str;

    }
}
