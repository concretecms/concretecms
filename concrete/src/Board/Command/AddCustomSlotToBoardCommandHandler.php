<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class AddCustomSlotToBoardCommandHandler extends BoardSlotCommandHandler
{

    protected function getRuleType($command)
    {
        return InstanceSlotRule::RULE_TYPE_EDITOR_CUSTOM_SLOT_CONTENT;
    }

}
