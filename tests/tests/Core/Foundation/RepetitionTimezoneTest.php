<?php
namespace Concrete\Tests\Core\Foundation;

use Concrete\Core\Application\Application as ServiceLocator;
use Concrete\Core\Foundation\Repetition\AbstractRepetition;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Facade;

class TestRepetition extends AbstractRepetition
{

    public function save()
    {
        return false;
    }

    public function getID()
    {
        return false;
    }

}

class RepetitionTimezoneTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $dateService Date
     */
    protected $dateService;

    protected function setUp()
    {
        parent::setUp();
        $this->dateService = new Date();
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


    public function testSingleOccurrence()
    {
        $old = $this->setTimezone('America/Los_Angeles');

        $repetition = new TestRepetition();

        $this->assertEquals('America/Los_Angeles', $repetition->getTimezone()->getName());

        $repetition->setStartDate('2017-08-01 09:00:00'); // Since we're defaulting to the site time of America/Los Angeles it's 9am
        $start_time = $this->dateService->toDateTime($repetition->getStartDate())->getTimestamp();
        $end_time = $this->dateService->toDateTime('+5 years')->getTimestamp();
        $this->assertEquals(1501603200, $start_time);

        $initial_occurrence_time = $this->dateService->toDateTime($repetition->getStartDate())->getTimestamp();
        if ($repetition->getEndDate()) {
            $initial_occurrence_time_end = $this->dateService->toDateTime($repetition->getEndDate())->getTimestamp();
        } else {
            $initial_occurrence_time_end = $initial_occurrence_time;
        }

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1, count($occurrences));
        $this->assertEquals($occurrences[0][0], $initial_occurrence_time);
        $this->assertEquals($occurrences[0][1], $initial_occurrence_time_end);

        $this->resetTimezone($old);
    }

    public function testSingleOccurrenceDifferentZone()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new TestRepetition('America/Los_Angeles');

        $repetition->setStartDate('2017-08-01 16:00:00'); // 8/1/17 4PM Pacific, should be 8/1/17 23:00
        $start_time = $this->dateService->toDateTime($repetition->getStartDate(), 'system', $repetition->getTimezone()->getName())->getTimestamp();
        $this->assertEquals(1501628400, $start_time);
        $this->assertEquals(1501628400, $repetition->getStartDateTimestamp());

        $this->resetTimezone($old);
    }

    public function testDaily()
    {
        $old = $this->setTimezone('GMT');

        $repetition = new TestRepetition('America/Los_Angeles');
        $repetition->setStartDate('2017-03-27 20:00:00'); // 8 PM PST
        $repetition->setEndDate('2017-03-27 22:00:00'); // 8 PM - 10 PM
        $repetition->setRepeatPeriod(TestRepetition::REPEAT_DAILY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodEnd('2017-04-05');
        $start_time = $repetition->getStartDateTimestamp();
        $end_time = $this->dateService->toDateTime('+5 years')->getTimestamp();

        $this->assertEquals(1490670000, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);
        $this->assertEquals(10, count($occurrences));

        $this->assertEquals(1490670000, $occurrences[0][0]); // 3/28/17 3:00 am GMT
        $this->assertEquals(1490677200, $occurrences[0][1]); // 3/28/17 5:00 am GMT
        $this->assertEquals(1491447600, $occurrences[9][0]); // 3/28/17 3:00 am GMT
        $this->assertEquals(1491454800, $occurrences[9][1]); // 3/28/17 5:00 am GMT

        $this->resetTimezone($old);
    }

    public function testWeekly()
    {
        $old = $this->setTimezone('Europe/Paris');

        $repetition = new TestRepetition('America/Los_Angeles');
        $repetition->setStartDate('2017-03-03 21:00'); // 9 PM PST
        $repetition->setEndDate('2017-03-04 02:00:00'); // 9-2AM
        $repetition->setRepeatPeriod(TestRepetition::REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(1);
        $repetition->setRepeatPeriodWeekDays([5]); // every Friday
        $repetition->setRepeatPeriodEnd('2017-07-07');

        $start_time = $repetition->getStartDateTimestamp();
        $end_time = $this->dateService->toDateTime('+5 years')->getTimestamp();

        $this->assertEquals(1488603600, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);
        $this->assertEquals(19, count($occurrences));
        $this->resetTimezone($old);

        $this->assertEquals(1488603600, $occurrences[0][0]);
        $this->assertEquals(1488621600, $occurrences[0][1]);
        $this->assertEquals(1491019200, $occurrences[4][0]);
        $this->assertEquals(1491037200, $occurrences[4][1]);
        $this->assertEquals(1499486400, $occurrences[18][0]);
        $this->assertEquals(1499504400, $occurrences[18][1]);
    }

    public function testMonthlyFirstByDate()
    {
        $old = $this->setTimezone('Pacific/Honolulu');

        $repetition = new TestRepetition('Europe/Paris');
        $repetition->setStartDate('2017-05-01 8:00:00');
        $repetition->setEndDate('2017-05-01 11:00:00');
        $repetition->setRepeatPeriod(TestRepetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy(TestRepetition::MONTHLY_REPEAT_MONTHLY);
        $repetition->setRepeatEveryNum(2);

        $start_time = $repetition->getStartDateTimestamp();
        $end_time = $this->dateService->toDateTime('+2 years')->getTimestamp();

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

        $repetition = new TestRepetition('America/Chicago');
        $repetition->setStartDate('2017-06-07 18:30:00'); // every first wednesday
        $repetition->setEndDate('2017-06-07 20:00:00');
        $repetition->setRepeatPeriodWeekDays([3]); // every wednesday
        $repetition->setRepeatPeriod(TestRepetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy(TestRepetition::MONTHLY_REPEAT_WEEKLY); // Boxing every Wednesday
        $repetition->setRepeatEveryNum(1);

        $start_time = $repetition->getStartDateTimestamp();
        $end_time = $this->dateService->toDateTime('+1 years')->getTimestamp();

        $this->assertEquals(1496878200, $repetition->getStartDateTimestamp());

        $occurrences = $repetition->activeRangesBetween($start_time, $end_time);

        $this->assertEquals(1496878200, $occurrences[0][0]);
        $this->assertEquals(1496883600, $occurrences[0][1]);

        $this->assertEquals(12, count($occurrences));

        $this->resetTimezone($old);


    }


}
