<?php
namespace Concrete\Core\User\Point;

use Concrete\Core\Legacy\DatabaseItemList;

class ActivityList extends DatabaseItemList
{
    protected $itemsPerPage = 50;

    public function __construct()
    {
        $this->setQuery('select sum(upPoints) as total, upuID from UserPointHistory inner join UserPointActions on UserPointHistory.upaID = UserPointActions.upaID');
        $this->groupBy('upuID');
        $this->sortBy('total', 'desc');
    }

    public function filterByTimePeriod($period)
    {
        $ts = date('Y-m-d H:i:s', strtotime($period));
        $this->filter('timestamp', $ts, '>=');
    }

    public function filterByTimePeriodRange($periodFrom, $periodTo)
    {
        $ts1 = date('Y-m-d H:i:s', strtotime($periodFrom));
        $ts2 = date('Y-m-d H:i:s', strtotime($periodTo));
        $this->filter('timestamp', $ts1, '>=');
        $this->filter('timestamp', $ts2, '<=');
    }

    public function filterByAction($upaID)
    {
        $this->filter('upaID', $upaID);
    }
}
