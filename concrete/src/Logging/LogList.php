<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Legacy\DatabaseItemList;
use Database;

class LogList extends DatabaseItemList
{
    protected $autoSortColumns = ['logID', 'level'];
    protected $sortBy = 'logID';
    protected $sortByDirection = 'desc';

    public function __construct()
    {
        $this->setQuery('select Logs.logID from Logs');
    }

    public function get($itemsToGet = 100, $offset = 0)
    {
        $r = parent::get($itemsToGet, intval($offset));
        $entries = [];
        foreach ($r as $row) {
            $e = LogEntry::getByID($row['logID']);
            if (is_object($e)) {
                $entries[] = $e;
            }
        }

        return $entries;
    }

    public function filterByKeywords($keywords)
    {
        $db = Database::get();
        $this->filter(false, 'message like ' . $db->qstr('%' . $keywords . '%'));
    }

    public function filterByChannel($channel)
    {
        $this->filter('channel', $channel);
    }

    public function filterByLevels($levels)
    {
        $db = Database::get();
        if (is_array($levels)) {
            $lth = '(';
            for ($i = 0, $iMax = count($levels); $i < $iMax; ++$i) {
                if ($i > 0) {
                    $lth .= ',';
                }
                $lth .= $db->quote($levels[$i]);
            }
            $lth .= ')';
            $this->filter(false, "(level in {$lth})");
        }
    }

    public function filterByTime($time, $comparison = '=')
    {
        $db = Database::get();
        $time = $db->quote($time);
        $this->userPostQuery .= ' AND time ' . $comparison . ' ' . $time;
    }
}
