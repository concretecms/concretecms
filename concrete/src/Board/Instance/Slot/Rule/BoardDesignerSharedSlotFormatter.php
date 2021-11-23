<?php

namespace Concrete\Core\Board\Instance\Slot\Rule;

use Concrete\Core\Entity\Board\InstanceSlotRule;

class BoardDesignerSharedSlotFormatter extends AbstractFormatter
{

    public function getRuleActionDescription(InstanceSlotRule $rule): string
    {
        if ($rule->getUser()) {
            $user = $rule->getUser()->getUserName();
        } else {
            $user = t('(Unknown User)');
        }
        $date = new \DateTime('@' . $rule->getDateCreated(), new \DateTimeZone($rule->getTimezone()));
        if ($rule->isLocked()) {
            return t(/*i18n: %1$s is a user name, %2$s is a date */'Created within the board designer and locked by %1$s on %2$s', $user, $date->format('n/j/Y'));
        } else {
            return t(/*i18n: %1$s is a user name, %2$s is a date */'Created within the board designer and shared by %1$s on %2$s', $user, $date->format('n/j/Y'));
        }
    }


}

