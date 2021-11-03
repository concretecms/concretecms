<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

abstract class AbstractFormatter implements FormatterInterface
{

    public function getRuleName(InstanceSlotRule $rule) : string
    {
        $name = $rule->getNotes();
        if (!$name) {
            $name = t('(No Name)');
        }
        return $name;
    }


}

