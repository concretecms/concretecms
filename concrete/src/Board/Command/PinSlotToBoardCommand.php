<?php

namespace Concrete\Core\Board\Command;

class PinSlotToBoardCommand
{

    use BoardInstanceTrait;

    /**
     * @var int
     */
    protected $slot = 0;

    /**
     * @var int
     */
    protected $bID;

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
     * @return int
     */
    public function getBlockID(): int
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



}
