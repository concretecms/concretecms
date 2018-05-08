<?php
namespace Concrete\Core\User\Point;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Legacy\DatabaseItemList;
use Concrete\Core\Support\Facade\Application;
use Loader;
use Concrete\Core\User\Point\Entry as UserPointEntry;

class EntryList extends DatabaseItemList
{
    protected $autoSortColumns = array('uName', 'upaName', 'upPoints', 'timestamp');

    public function __construct()
    {
        $this->setBaseQuery();
    }

    protected function setBaseQuery()
    {
        $db = Application::getFacadeApplication()->make(Connection::class);
        $this->setQuery('SELECT UserPointHistory.upID
			FROM UserPointHistory
			LEFT JOIN UserPointActions ON UserPointActions.upaID = UserPointHistory.upaID
			LEFT JOIN ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' ON UserPointActions.gBadgeID = Groups.gID
			LEFT JOIN Users ON UserPointHistory.upuID = Users.uID
		');
    }

    /**
     * @param int $gID
     */
    public function filterByGroupID($gID)
    {
        $this->filter('UserPointActions.gBadgeID', $gID);
    }

    /**
     * @param string $uName
     */
    public function filterByUserName($uName)
    {
        $this->filter('Users.uName', $uName);
    }

    public function filterByUserPointActionName($upaName)
    {
        $db = Loader::db();
        $this->filter(false, "UserPointActions.upaName LIKE ".$db->quote('%'.$upaName.'%'));
    }

    /**
     * @param int $uID
     */
    public function filterByUserID($uID)
    {
        $this->filter('UserPointHistory.upuID', (int) $uID);
    }

    public function get($items = 0, $offset = 0)
    {
        $resp = parent::get($items, $offset);
        $entries = array();
        foreach ($resp as $r) {
            $up = new UserPointEntry();
            $up->load($r['upID']);
            $entries[] = $up;
        }

        return $entries;
    }
}
