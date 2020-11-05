<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class BoardDesignerLockedSlotFormatter implements FormatterInterface
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
        return t(/*i18n: %1$s is a user name, %2$s is a date */'Locked by %1$s on %2$s', $user, $date->format('n/j/Y'));
    }


}

