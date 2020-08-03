<?php

namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\Search\ColumnSet\Available;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Facade;

class LogListPagerManager extends AbstractPagerManager
{

    /**
     * @param LogEntry $mixed
     * @return int
     */
    public function getCursorStartValue($mixed)
    {
        return $mixed->getId();
    }

    public function getCursorObject($cursor)
    {
        $app = Facade::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $row = $db->fetchArray("SELECT * FROM Logs WHERE logID = ?", [$cursor]);
        return new LogEntry($row);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('l.logID', $direction);
    }

}