<?php

namespace Concrete\Core\User\Point\Action;

class WonBadgeAction extends Action
{
    public function addDetailedEntry($user, $group)
    {
        $obj = new WonBadgeActionDescription();
        $obj->setBadgeGroupID($group->getGroupID());
        $entry = self::addEntry($user, $obj, $group->getGroupBadgeCommunityPointValue());
    }
}
