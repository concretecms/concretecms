<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

interface FormatterInterface
{

    public function getRuleActionDescription(InstanceSlotRule $rule): string;

    public function getRuleName(InstanceSlotRule $rule): string;

}

