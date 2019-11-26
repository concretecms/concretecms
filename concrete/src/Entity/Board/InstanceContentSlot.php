<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="BoardInstanceContentSlots"
 * )
 */
class InstanceContentSlot
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $boardInstanceContentSlotID;

    /**
     * @ORM\ManyToOne(targetEntity="InstanceSlot", inversedBy="content_slots")
     * @ORM\JoinColumn(name="boardInstanceSlotID", referencedColumnName="boardInstanceSlotID")
     */
    protected $instance_slot;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $slot;

    /**
     * @return mixed
     */
    public function getBoardInstanceContentSlotID()
    {
        return $this->boardInstanceContentSlotID;
    }

    /**
     * @return mixed
     */
    public function getInstanceSlot()
    {
        return $this->instance_slot;
    }

    /**
     * @param mixed $instance_slot
     */
    public function setInstanceSlot($instance_slot): void
    {
        $this->instance_slot = $instance_slot;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($slot): void
    {
        $this->slot = $slot;
    }
    
    
    

    
    
    
}
