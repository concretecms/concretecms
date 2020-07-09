<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class SlotPinnedFormatter implements FormatterInterface
{

    public function getRuleName(InstanceSlotRule $rule) : string
    {
        return t('Slot %s pinned from editing interface.', $rule->getSlot());
    }

    public function getRuleActionDescription(InstanceSlotRule $rule): string
    {
        if ($rule->getUser()) {
            $user = $rule->getUser()->getUserName();
        } else {
            $user = t('(Unknown User)');
        }
        $date = new \DateTime('@' . $rule->getDateCreated(), new \DateTimeZone($rule->getTimezone()));
        return t('Pinned by %s on %s', $user, $date->format('n/j/Y'));
    }

}

