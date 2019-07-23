<?php
namespace Concrete\Core\User\Point\Action;

use Concrete\Core\Legacy\DatabaseItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\Connection\Connection;

class ActionList extends DatabaseItemList
{
    public function __construct()
    {
        $this->setBaseQuery();
    }

    protected function setBaseQuery()
    {
        $db = Application::getFacadeApplication()->make(Connection::class);
        $this->setQuery('SELECT UserPointActions.*, Groups.gName FROM UserPointActions LEFT JOIN ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' ON Groups.gID = UserPointActions.gBadgeID');
    }

    public function filterByIsActive($active)
    {
        $this->filter('upaIsActive', $active);
    }
}
