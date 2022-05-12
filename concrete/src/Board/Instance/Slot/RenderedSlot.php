<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;

class RenderedSlot implements \JsonSerializable
{

    const SLOT_TYPE_AUTOMATIC = 'S'; // This is the default
    const SLOT_TYPE_PINNED = 'P'; // An automatic slot was pinned
    const SLOT_TYPE_CUSTOM = 'C'; // The in-page slot builder or designer slot builder was used.

    /**
     * @var string
     */
    protected $slotType = self::SLOT_TYPE_AUTOMATIC;

    /**
     * @var bool
     */
    protected $isLocked = false;

    /**
     * @var int
     */
    protected $bID;

    /**
     * Does the current rendered slot contain content based on a board instance rule? If so the ID of the rule
     * is present here
     *
     * @var int
     */
    protected $boardInstanceSlotRuleID;

    /**
     * @var int
     */
    protected $slot;

    /**
     * @var Instance
     */
    protected $instance;

    /**
     * State constructor.
     * @param InstanceSlot $slot
     */
    public function __construct(Instance $instance, int $slot)
    {
        $this->instance = $instance;
        $this->slot = $slot;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @param bool $isLocked
     */
    public function setIsLocked(bool $isLocked): void
    {
        $this->isLocked = $isLocked;
    }

    /**
     * @return int
     */
    public function getBlockID(): ?int
    {
        return $this->bID;
    }

    /**
     * @param int $bID
     */
    public function setBlockID(int $bID): void
    {
        $this->bID = $bID;
    }

    /**
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @param int $slot
     */
    public function setSlot(int $slot): void
    {
        $this->slot = $slot;
    }

    /**
     * @return Instance
     */
    public function getInstance(): Instance
    {
        return $this->instance;
    }

    /**
     * @param Instance $instance
     */
    public function setInstance(Instance $instance): void
    {
        $this->instance = $instance;
    }

    /**
     * @return string
     */
    public function getSlotType(): string
    {
        return $this->slotType;
    }

    /**
     * @param string $slotType
     */
    public function setSlotType(string $slotType): void
    {
        $this->slotType = $slotType;
    }

    /**
     * @return int
     */
    public function getBoardInstanceSlotRuleID(): ?int
    {
        return $this->boardInstanceSlotRuleID;
    }

    /**
     * @param int $boardInstanceSlotRuleID
     */
    public function setBoardInstanceSlotRuleID(int $boardInstanceSlotRuleID): void
    {
        $this->boardInstanceSlotRuleID = $boardInstanceSlotRuleID;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'boardInstanceID' => $this->instance->getBoardInstanceID(),
            'boardInstanceSlotRuleID' => $this->getBoardInstanceSlotRuleID(),
            'slot' => $this->slot,
            'bID' => $this->bID,
            'slotType' => $this->getSlotType(),
            'isLocked' => $this->isLocked()
        ];
    }

}

