<?php

namespace Concrete\Core\User\Group;

use Concrete\Core\Legacy\DatabaseItemList;

class GroupSetList extends DatabaseItemList
{
    public function __construct()
    {
        $this->setQuery('select gsID from GroupSets');
        $this->sortBy('gsName', 'asc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        $r = parent::get($itemsToGet, $offset);
        $groupsets = array();
        foreach ($r as $row) {
            $groupsets[] = GroupSet::getByID($row['gsID']);
        }

        return $groupsets;
    }
}
