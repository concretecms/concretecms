<?php

namespace Concrete\Core\Board\Designer\Command;


use Concrete\Core\Board\Command\BoardSlotCommandHandler;
use Concrete\Core\Entity\Board\InstanceSlotRule;

class AddDesignerSlotToBoardCommandHandler extends BoardSlotCommandHandler
{

    /**
     * @param AddDesignerSlotToBoardCommand $command
     * @return string
     */
    public function getRuleType($command)
    {
        return $command->getLockType();
    }

}
