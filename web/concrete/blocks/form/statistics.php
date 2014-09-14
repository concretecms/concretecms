<?php
namespace Concrete\Block\Form;

use Loader;
use Core;

class Statistics
{
    /**
     * Gets the total number of submissions
     * @param string $date Set to a specific day (eg '2014-09-14') to retrieve the submissions in that day.
     * @param string $dateTimezone The timezone of the $date parameter (acceptable values: 'user', 'system', 'app' or any valid PHP timezone identifier)
     * @return int
     */
    public static function getTotalSubmissions($date = null, $dateTimezone = 'user')
    {
        if ($date) {
           return static::getTotalSubmissionsBetween("$date 00:00:00", "$date 23:59:59", $dateTimezone);
        } else {
           return static::getTotalSubmissionsBetween();
        }
    }
    /**
     * Gets the total number of submissions in specific date/time ranges
     * @param string|int|\DateTime $fromDate The start of the period (if empty: from ever). Inclusive. Example: '2014-09-14 08:00:00'.
     * @param string|int|\DateTime $toDate The end of the period (if empty: for ever). Inclusive. Example: '2014-09-14 08:00:00'.
     * @param string $dateTimezone The timezone of the $dateFrom and $dateTo parameter (acceptable values: 'user', 'system', 'app' or any valid PHP timezone identifier)
     * @return number
     */
    public static function getTotalSubmissionsBetween($fromDate = null, $toDate = null, $datesTimezone = 'user')
    {
        $dh = Core::make('helper/date');
        /* @var $dh \Concrete\Core\Localization\Service\Date */
        if ($fromDate) {
            $fromDate = $dh->toDB($fromDate, $datesTimezone);
        }
        if ($toDate) {
            $toDate = $dh->toDB($toDate, $datesTimezone);
        }
        $where = '';
        $q = array();
        if ($fromDate && $toDate) {
            $where = ' where created between ? and ?';
            $q[] = $fromDate;
            $q[] = $toDate;
        } elseif ($fromDate) {
            $where = ' where created >= ?';
            $q[] = $fromDate;
        } elseif ($toDate) {
            $where = ' where created <= ?';
            $q[] = $toDate;
        }
        $count = Loader::db()->GetOne('select count(asID) from btFormAnswerSet' . $where, $q);

        return empty($count) ? 0 : intval($count);
    }

    public static function loadSurveys($MiniSurvey)
    {
        $db = Loader::db();

        return $db->query('SELECT s.* FROM ' . $MiniSurvey->btTable . ' AS s, Blocks AS b, BlockTypes AS bt
            WHERE s.bID=b.bID AND b.btID=bt.btID AND bt.btHandle="form" AND EXISTS (
            SELECT 1 FROM CollectionVersionBlocks cvb
            INNER JOIN CollectionVersions cv ON cvb.cID=cv.cID AND cvb.cvID=cv.cvID
            INNER JOIN Pages p ON cv.cID = p.cID
            WHERE cvb.bID=s.bID AND p.cIsActive=1 AND cv.cvIsApproved=1
         )');
    }

    public static $sortChoices = array('newest' => 'created DESC' , 'chrono'=>'created');

    public static function buildAnswerSetsArray($questionSet, $orderBy='', $limit='')
    {
        $db = Loader::db();

        if ((strlen(trim($limit)) > 0) && (!strstr(strtolower($limit),'limit'))) {
            $limit = ' LIMIT ' . $limit;
        }
        if ((strlen(trim($orderBy)) > 0) && array_key_exists($orderBy, self::$sortChoices)) {
             $orderBySQL = self::$sortChoices[$orderBy];
        } else {
            $orderBySQL = self::$sortChoices['newest'];
        }

        //get answers sets
        $sql = 'SELECT * FROM btFormAnswerSet AS aSet ' .
            'WHERE aSet.questionSetId=' . $questionSet . ' ORDER BY ' . $orderBySQL . ' ' . $limit;
        $answerSetsRS = $db->query($sql);
        //load answers into a nicer multi-dimensional array
        $answerSets = array();
        $answerSetIds = array(0);
        while ($answer = $answerSetsRS->fetchRow()) {
            //answer set id - question id
            $answerSets[$answer['asID']] = $answer;
            $answerSetIds[] = $answer['asID'];
        }

        //get answers
        $sql = 'SELECT * FROM btFormAnswers AS a WHERE a.asID IN (' . join(',', $answerSetIds) . ')';
        $answersRS = $db->query($sql);

        //load answers into a nicer multi-dimensional array
        while ($answer = $answersRS->fetchRow()) {
            //answer set id - question id
            $answerSets[$answer['asID']]['answers'][$answer['msqID']] = $answer;
        }

        return $answerSets;
    }
}
