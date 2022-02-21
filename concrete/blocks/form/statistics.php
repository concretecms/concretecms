<?php

namespace Concrete\Block\Form;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;

class Statistics
{
    /**
     * @var string[]
     */
    public static $sortChoices = ['newest' => 'created DESC', 'chrono' => 'created'];

    /**
     * Gets the total number of submissions.
     *
     * @param string|null $date set to a specific day (eg '2014-09-14') to retrieve the submissions in that day
     * @param string $dateTimezone The timezone of the $date parameter (acceptable values: 'user', 'system', 'app' or any valid PHP timezone identifier)
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Punic\Exception\BadArgumentType
     *
     * @return int
     */
    public static function getTotalSubmissions($date = null, $dateTimezone = 'user')
    {
        if ($date) {
            return static::getTotalSubmissionsBetween("{$date} 00:00:00", "{$date} 23:59:59", $dateTimezone);
        }

            return static::getTotalSubmissionsBetween();
    }

    /**
     * Gets the total number of submissions in specific date/time ranges.
     *
     * @param string|int|\DateTime $fromDate The start of the period (if empty: from ever). Inclusive. Example: '2014-09-14 08:00:00'.
     * @param string|int|\DateTime $toDate The end of the period (if empty: for ever). Inclusive. Example: '2014-09-14 08:00:00'.
     * @param string $datesTimezone The timezone of the $dateFrom and $dateTo parameter (acceptable values: 'user', 'system', 'app' or any valid PHP timezone identifier)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Punic\Exception\BadArgumentType
     *
     * @return int
     */
    public static function getTotalSubmissionsBetween($fromDate = null, $toDate = null, $datesTimezone = 'user')
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make('database/connection');
        /** @var \Concrete\Core\Localization\Service\Date $dh */
        $dh = $app->make('helper/date');

        if ($fromDate) {
            $fromDate = $dh->toDB($fromDate, $datesTimezone);
        }
        if ($toDate) {
            $toDate = $dh->toDB($toDate, $datesTimezone);
        }
        $where = '';
        $q = [];
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

        $count = $db->fetchOne('select count(asID) from btFormAnswerSet' . $where, $q);

        return empty($count) ? 0 : (int) $count;
    }

    /**
     * @param MiniSurvey $MiniSurvey
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Doctrine\DBAL\ForwardCompatibility\DriverResultStatement<mixed>|\Doctrine\DBAL\ForwardCompatibility\DriverStatement<mixed>|\Doctrine\DBAL\ForwardCompatibility\Result<mixed>
     */
    public static function loadSurveys($MiniSurvey)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make('database/connection');
        $dh = $app->make('date');
        $now = $dh->getOverridableNow();

        return $db->executeQuery('SELECT s.* FROM ' . $MiniSurvey->btTable . ' AS s, Blocks AS b, BlockTypes AS bt
            WHERE s.bID=b.bID AND b.btID=bt.btID AND bt.btHandle="form" AND EXISTS (
            SELECT 1 FROM CollectionVersionBlocks cvb
            INNER JOIN CollectionVersions cv ON cvb.cID=cv.cID AND cvb.cvID=cv.cvID AND 1=cv.cvIsApproved AND (cv.cvPublishDate IS NULL OR cv.cvPublishDate <= ?) AND (cv.cvPublishEndDate IS NULL OR cv.cvPublishEndDate >= ?)
            INNER JOIN Pages p ON cv.cID = p.cID
            WHERE cvb.bID=s.bID AND p.cIsActive=1
         )', [$now, $now]);
    }

    /**
     * @param int $questionSet
     * @param string $orderBy
     * @param int|string $limit
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<string,mixed>
     */
    public static function buildAnswerSetsArray($questionSet, $orderBy = '', $limit = '')
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make('database/connection');

        if ((trim($limit) !== '') && (stripos($limit, 'limit') === false)) {
            $limit = ' LIMIT ' . $limit;
        }
        if (array_key_exists($orderBy, self::$sortChoices)) {
            $orderBySQL = self::$sortChoices[$orderBy];
        } else {
            $orderBySQL = self::$sortChoices['newest'];
        }

        //get answers sets
        $sql = 'SELECT * FROM btFormAnswerSet AS aSet ' .
            'WHERE aSet.questionSetId=' . $questionSet . ' ORDER BY ' . $orderBySQL . ' ' . $limit;
        $answerSetsRS = $db->executeQuery($sql);
        //load answers into a nicer multi-dimensional array
        $answerSets = [];
        $answerSetIds = [0];
        $answers = $answerSetsRS->fetchAllAssociative();

        foreach ($answers as $answer) {
            //answer set id - question id
            $answerSets[$answer['asID']] = $answer;
            $answerSetIds[] = $answer['asID'];
        }

        //get answers
        $sql = 'SELECT * FROM btFormAnswers AS a WHERE a.asID IN (' . implode(',', $answerSetIds) . ')';
        $answersRS = $db->executeQuery($sql)->fetchAllAssociative();

        //load answers into a nicer multi-dimensional array
        foreach ($answersRS as $answer) {
            //answer set id - question id
            $answerSets[$answer['asID']]['answers'][$answer['msqID']] = $answer;
        }

        return $answerSets;
    }
}
