<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class PinSlotToBoardCommandHandler extends BoardSlotCommandHandler
{

    protected function getRuleType($command)
    {
        return InstanceSlotRule::RULE_TYPE_AUTOMATIC_SLOT_PINNED;
    }

}
