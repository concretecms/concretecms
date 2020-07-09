<?php

namespace Concrete\Core\Board\Command;

class ClearSlotFromBoardCommand
{

    use BoardInstanceTrait;

    /**
     * @var int
     */
    protected $slot = 0;

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




}
