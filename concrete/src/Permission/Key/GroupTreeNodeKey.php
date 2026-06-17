<?php
namespace Concrete\Core\Permission\Key;

class GroupTreeNodeKey extends TreeNodeKey
{
    /**
     * Used by the user search when filtering by group.
     */
    public function canSearchUsersInGroup()
    {
        // Use the standard permission validation for this key.
        return $this->validate('search_users_in_group');
    }
}
