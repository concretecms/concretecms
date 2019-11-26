<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="BoardInstanceSlots"
 * )
 */
class InstanceSlot
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $boardInstanceSlotID;

    /**
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="slots")
     * @ORM\JoinColumn(name="boardInstanceID", referencedColumnName="boardInstanceID")
     */
    protected $instance;

    /**
     * @ORM\ManyToOne(targetEntity="SlotTemplate")
     */
    protected $template;

    /**
     * @ORM\Column(type="integer")
     */
    protected $slot;

    /**
     * @return mixed
     */
    public function getBoardInstanceSlotID()
    {
        return $this->boardInstanceSlotID;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template): void
    {
        $this->template = $template;
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
