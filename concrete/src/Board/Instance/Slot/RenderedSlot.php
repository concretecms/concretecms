<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;

class RenderedSlot implements \JsonSerializable
{

    /**
     * @var bool
     */
    protected $isPinned = false;

    /**
     * @var int
     */
    protected $bID;

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
    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    /**
     * @param bool $isPinned
     */
    public function setIsPinned(bool $isPinned): void
    {
        $this->isPinned = $isPinned;
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

    public function jsonSerialize()
    {
        return [
            'boardInstanceID' => $this->instance->getBoardInstanceID(),
            'isPinned' => $this->isPinned,
            'slot' => $this->slot,
            'bID' => $this->bID,
        ];
    }

}

