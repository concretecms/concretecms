<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class BoardDesignerSharedSlotFormatter implements FormatterInterface
{

    public function getRuleName(InstanceSlotRule $rule) : string
    {
        $name = $rule->getNotes();
        if (!$name) {
            $name = t('(No Name)');
        }
        return $name;
    }

    public function getRuleActionDescription(InstanceSlotRule $rule): string
    {
        if ($rule->getUser()) {
            $user = $rule->getUser()->getUserName();
        } else {
            $user = t('(Unknown User)');
        }
        $date = new \DateTime('@' . $rule->getDateCreated(), new \DateTimeZone($rule->getTimezone()));
        if ($rule->isLocked()) {
            return t('Locked by %s on %s', $user, $date->format('n/j/Y'));
        } else {
            return t('Shared by %s on %s', $user, $date->format('n/j/Y'));
        }
    }


}

