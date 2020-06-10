<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class PinSlotToBoardCommandHandler extends BoardSlotCommandHandler
{

    protected function getRuleType()
    {
        return InstanceSlotRule::RULE_TYPE_PINNED;
    }

}
