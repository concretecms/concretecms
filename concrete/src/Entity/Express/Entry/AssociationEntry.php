<?php
namespace Concrete\Core\Entity\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressEntityAssociationEntries")
 */
class AssociationEntry
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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Entry\Association", inversedBy="selectedEntries")
     */
    protected $association;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $displayOrder = 0;

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

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * @param mixed $displayOrder
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }






}
