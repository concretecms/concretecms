<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Board\Command\BoardSlotCommand;

class AddDesignerSlotToBoardCommand extends BoardSlotCommand
{

    protected $lockType;

    /**
     * @return mixed
     */
    public function getLockType()
    {
        return $this->lockType;
    }

    /**
     * @param mixed $lockType
     */
    public function setLockType($lockType): void
    {
        $this->lockType = $lockType;
    }


}
