<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class CustomSlotContentFormatter implements FormatterInterface
{

    public function getRuleName(InstanceSlotRule $rule) : string
    {
        return t('Custom slot created for slot %s.', $rule->getSlot());
    }

    public function getRuleActionDescription(InstanceSlotRule $rule): string
    {
        if ($rule->getUser()) {
            $user = $rule->getUser()->getUserName();
        } else {
            $user = t('(Unknown User)');
        }
        $date = new \DateTime('@' . $rule->getDateCreated(), new \DateTimeZone($rule->getTimezone()));
        return t('Created by %s on %s', $user, $date->format('n/j/Y'));
    }

}

