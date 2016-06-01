<?php
namespace Concrete\Core\User\Point\Action;

use Concrete\Core\Legacy\DatabaseItemList;

class ActionList extends DatabaseItemList
{
    public function __construct()
    {
        $this->setBaseQuery();
    }

    protected function setBaseQuery()
    {
        $this->setQuery('SELECT UserPointActions.*, Groups.gName FROM UserPointActions LEFT JOIN Groups ON Groups.gID = UserPointActions.gBadgeID');
    }

    public function filterByIsActive($active)
    {
        $this->filter('upaIsActive', $active);
    }
}
