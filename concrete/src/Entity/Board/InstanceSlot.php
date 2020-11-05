<?php
namespace Concrete\Core\Entity\Board;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\EntityListeners({"\Concrete\Core\Board\Instance\Slot\Listener"})
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
     * @ORM\Column(type="integer")
     */
    protected $bID = 0;


    public function __construct()
    {
        $this->content_slots = new ArrayCollection();
    }

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
    public function getTemplate() : SlotTemplate
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

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance): void
    {
        $this->instance = $instance;
    }

    /**
     * @return mixed
     */
    public function getContentSlots()
    {
        return $this->content_slots;
    }

    /**
     * @param mixed $content_slots
     */
    public function setContentSlots($content_slots): void
    {
        $this->content_slots = $content_slots;
    }

    /**
     * @return mixed
     */
    public function getBlockID()
    {
        return $this->bID;
    }

    /**
     * @param mixed $bID
     */
    public function setBlockID($bID): void
    {
        $this->bID = $bID;
    }


}
