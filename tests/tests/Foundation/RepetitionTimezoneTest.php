<?php

namespace Concrete\Tests\Foundation;

use Concrete\Core\Foundation\Repetition\BasicRepetition;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Facade;
use PHPUnit_Framework_TestCase;

class RepetitionTimezoneTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Date
     */
    protected $dateService;

    protected function setUp()
    {
        parent::setUp();
        $this->dateService = new Date();
    }

    public function testSingleOccurrence()
    {
        $old = $this->setTimezone('America/Los_Angeles');

        $repetition = new BasicRepetition();

        $this->assertEquals('America/Los_Angeles', $repetition->getTimezone()->getName());

        $repetition->setStartDate('2017-08-01 09:00:00'); // Since we're defaulting to the site time of America/Los Angeles it's 9am
        $start_time = $this->dateService->toDateTime($repetition->getStartDate())->getTimestamp();

        $end_date_time = new \DateTime('2022-04-25', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1501603200, $start_time);

        $initial_occurrence = $this->generateInitialOccurrence($repetition);

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1, count($occurrences));
        $this->assertEquals($occurrences[0][0], $initial_occurrence[0]);
        $this->assertEquals($occurrences[0][1], $initial_occurrence[1]);

        $this->resetTimezone($old);
    }

    public function testSingleOccurrence2()
    {
        $old = $this->setTimezone('America/Los_Angeles');

        $repetition = new BasicRepetition('America/Chicago');
        $repetition->setStartDate('2017-05-31 20:00:00');
        $repetition->setEndDate('2017-05-31 21:00:00');
        $start = $repetition->getStartDateTimestamp() - 1;
        $datetime = new \DateTime('2022-05-31', $repetition->getTimezone());
        $end = $datetime->getTimestamp();

        $initial_occurrence = $this->generateInitialOccurrence($repetition);
        $occurrences = $repetition->activeRangesBetween($start, $end);

        $this->assertEquals(1, count($occurrences));

        $this->assertEquals(1496278800, $occurrences[0][0]);
        $this->assertEquals(1496282400, $occurrences[0][1]);

        $this->assertEquals($occurrences[0][0], $initial_occurrence[0]);
        $this->assertEquals($occurrences[0][1], $initial_occurrence[1]);

        $this->resetTimezone($old);
    }

    public function testSingleOccurrenceDifferentZone()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Los_Angeles');

        $repetition->setStartDate('2017-08-01 16:00:00'); // 8/1/17 4PM Pacific, should be 8/1/17 23:00
        $start_time = $this->dateService->toDateTime($repetition->getStartDate(), 'system', $repetition->getTimezone()->getName())->getTimestamp();
        $this->assertEquals(1501628400, $start_time);
        $this->assertEquals(1501628400, $repetition->getStartDateTimestamp());

        $this->resetTimezone($old);
    }

    public function testDaily()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Los_Angeles');
        $repetition->setStartDate('2017-03-27 20:00:00'); // 8 PM PST
        $repetition->setEndDate('2017-03-27 22:00:00'); // 8 PM - 10 PM
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_DAILY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodEnd('2017-04-05');
        $start_time = $repetition->getStartDateTimestamp();

        $end_date_time = new \DateTime('2022-04-25', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1490670000, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);
        $this->assertEquals(10, count($occurrences));

        $this->assertEquals(1490670000, $occurrences[0][0]); // 3/28/17 3:00 am GMT
        $this->assertEquals(1490677200, $occurrences[0][1]); // 3/28/17 5:00 am GMT
        $this->assertEquals(1491447600, $occurrences[9][0]); // 3/28/17 3:00 am GMT
        $this->assertEquals(1491454800, $occurrences[9][1]); // 3/28/17 5:00 am GMT

        $this->resetTimezone($old);
    }

    public function testDailyDST()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Los_Angeles');
        $repetition->setStartDate('2017-03-09 8:00:00');
        $repetition->setEndDate('2017-03-09 8:00:00'); // 8 PM - 10 PM
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_DAILY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodEnd('2017-03-28');
        $start_time = $repetition->getStartDateTimestamp();

        $end_date_time = new \DateTime('2022-04-25', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1489075200, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);
        $this->assertEquals(20, count($occurrences));

        $this->assertEquals(1489075200, $occurrences[0][0]);
        $this->assertEquals(1489075200, $occurrences[0][1]);
        $this->assertEquals(1489161600, $occurrences[1][0]);
        $this->assertEquals(1489161600, $occurrences[1][1]);
        $this->assertEquals(1489248000, $occurrences[2][0]);
        $this->assertEquals(1489248000, $occurrences[2][1]);
        $this->assertEquals(1489330800, $occurrences[3][0]);
        $this->assertEquals(1489330800, $occurrences[3][1]);
        $this->assertEquals(1489417200, $occurrences[4][0]);
        $this->assertEquals(1489417200, $occurrences[4][1]);

        $this->resetTimezone($old);
    }

    public function testWeekly()
    {
        $old = $this->setTimezone('Europe/Paris');

        $repetition = new BasicRepetition('America/Los_Angeles');
        $repetition->setStartDate('2017-03-03 21:00'); // 9 PM PST
        $repetition->setEndDate('2017-03-04 02:00:00'); // 9-2AM
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodWeekDays([5]); // every Friday
        $repetition->setRepeatPeriodEnd('2017-07-07');

        $start_time = $repetition->getStartDateTimestamp();
        $end_date_time = new \DateTime('2022-04-25', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1488603600, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);
        $this->assertEquals(19, count($occurrences));

        $this->assertEquals(1488603600, $occurrences[0][0]);
        $this->assertEquals(1488621600, $occurrences[0][1]);
        $this->assertEquals(1491019200, $occurrences[4][0]);
        $this->assertEquals(1491037200, $occurrences[4][1]);
        $this->assertEquals(1499486400, $occurrences[18][0]);
        $this->assertEquals(1499504400, $occurrences[18][1]);

        $this->resetTimezone($old);
    }

    public function testWeeklyOverDST()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Los_Angeles');
        $repetition->setStartDate('2016-11-15 16:00:00');
        $repetition->setEndDate('2016-11-15 13:00:00');
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodWeekDays([2]);
        $repetition->setRepeatPeriodEnd('2017-06-06');

        $start_time = $repetition->getStartDateTimestamp();
        $end_date_time = new \DateTime('2022-04-25', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1479254400, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1479254400, $occurrences[0][0]);
        $this->assertEquals(1479243600, $occurrences[0][1]);

        $this->assertEquals(1484092800, $occurrences[8][0]);
        $this->assertEquals(1484082000, $occurrences[8][1]);
        $this->assertEquals(1487721600, $occurrences[14][0]);
        $this->assertEquals(1487710800, $occurrences[14][1]);
        $this->assertEquals(1488931200, $occurrences[16][0]);
        $this->assertEquals(1488920400, $occurrences[16][1]);
        $this->assertEquals(1489532400, $occurrences[17][0]);
        $this->assertEquals(1489521600, $occurrences[17][1]);
        $this->assertEquals(1496790000, $occurrences[29][0]);
        $this->assertEquals(1496779200, $occurrences[29][1]);

        $this->assertEquals(30, count($occurrences));
        $this->resetTimezone($old);
    }

    public function testMonthlyFirstByDate()
    {
        $old = $this->setTimezone('Pacific/Honolulu');

        $repetition = new BasicRepetition('Europe/Paris');
        $repetition->setStartDate('2017-05-01 8:00:00');
        $repetition->setEndDate('2017-05-01 11:00:00');
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_MONTHLY);
        $repetition->setRepeatEveryNum(2);

        $start_time = $repetition->getStartDateTimestamp();
        $end_date_time = new \DateTime('2019-05-07', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1493618400, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1493618400, $occurrences[0][0]);
        $this->assertEquals(1493629200, $occurrences[0][1]);
        $this->assertEquals(1504245600, $occurrences[2][0]);
        $this->assertEquals(1504256400, $occurrences[2][1]);
        $this->assertEquals(1556690400, $occurrences[12][0]);
        $this->assertEquals(1556701200, $occurrences[12][1]);

        $this->assertEquals(13, count($occurrences));

        $this->resetTimezone($old);
    }

    public function testMonthlyFirstByWeek()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Chicago');
        $repetition->setStartDate('2017-03-08 18:30:00'); // every second wednesday
        $repetition->setEndDate('2017-03-08 20:00:00');
        $repetition->setRepeatPeriodWeekDays([3]); // every wednesday
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_WEEKLY); // Boxing every Wednesday
        $repetition->setRepeatEveryNum(1);

        $start_time = $repetition->getStartDateTimestamp();
        $end_date_time = new \DateTime('2018-05-07', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $this->assertEquals(1489019400, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1489019400, $occurrences[0][0]);
        $this->assertEquals(1489024800, $occurrences[0][1]);
        $this->assertEquals(1492039800, $occurrences[1][0]);
        $this->assertEquals(1492045200, $occurrences[1][1]);

        $this->assertEquals(14, count($occurrences));

        $this->resetTimezone($old);
    }

    public function testLastThursday()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new BasicRepetition('America/Los_Angeles');
        $repetition->setStartDate('2016-11-24 17:00:00');
        $repetition->setEndDate('2016-11-24 20:00:00');
        $repetition->setRepeatMonthLastWeekday(4);
        $repetition->setRepeatPeriod(BasicRepetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_LAST_WEEKDAY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodEnd('2018-01-01');

        $start_time = $repetition->getStartDateTimestamp();

        $this->assertEquals(1480035600, $repetition->getStartDateTimestamp());

        $end_date_time = new \DateTime('2030-05-07', $repetition->getTimezone());
        $end_time = $end_date_time->getTimestamp();

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1480035600, $occurrences[0][0]);
        $this->assertEquals(1480046400, $occurrences[0][1]);

        $this->assertEquals(1490918400, $occurrences[4][0]);
        $this->assertEquals(1490929200, $occurrences[4][1]);

        $this->assertEquals(1514509200, $occurrences[13][0]);
        $this->assertEquals(1514520000, $occurrences[13][1]);

        $this->assertEquals(14, count($occurrences));

        $this->resetTimezone($old);
    }

    protected function setTimezone($timezone)
    {
        $old = date_default_timezone_get();
        $app = Facade::getFacadeApplication();
        $config = $app->make('config');
        $config->set('app.server_timezone', $timezone);
        date_default_timezone_set($timezone);

        return $old;
    }

    protected function resetTimezone($old)
    {
        date_default_timezone_set($old);
        $app = Facade::getFacadeApplication();
        $config = $app->make('config');
        $config->set('app.server_timezone', null);
    }

    protected function generateInitialOccurrence($repetition)
    {
        $initial_occurrence_time = (new \DateTime($repetition->getStartDate(), $repetition->getTimezone()))
            ->getTimestamp();
        if ($repetition->getEndDate()) {
            $initial_occurrence_time_end = (new \DateTime($repetition->getEndDate(), $repetition->getTimezone()))
                ->getTimestamp();
        } else {
            $initial_occurrence_time_end = $initial_occurrence_time;
        }

        return [$initial_occurrence_time, $initial_occurrence_time_end];
    }
}
