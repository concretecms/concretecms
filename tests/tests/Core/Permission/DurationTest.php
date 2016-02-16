<?php
namespace tests\Core\Permission;

use Concrete\Core\Permission\Duration;

/**
 * Class DurationTest
 * Tests for `\Concrete\Core\Permission\Duration`.
 */
class DurationTest extends \PHPUnit_Framework_TestCase
{
    private static function getFarYear($wanted)
    {
        static $limitTo32bits;
        if (!isset($limitTo32bits)) {
            $limitTo32bits = @strtotime('2300-01-01') === false;
        }

        return $limitTo32bits ? min($wanted, 2037) : $wanted;
    }

    public function testDailyRecurring()
    {
        $now = strtotime('2/13/2014');

        // Setup
        $daily_repetition = new Duration();

        $daily_repetition->setStartDate(date('Y-m-d 1:00:00', $now));
        $daily_repetition->setEndDate(date('Y-m-d 3:00:00', strtotime('+1 day', $now)));

        $daily_repetition->setEndDateAllDay(false);
        $daily_repetition->setStartDateAllDay(false);

        $daily_repetition->setRepeatPeriod(Duration::REPEAT_DAILY);
        $daily_repetition->setRepeatEveryNum(100);

        $test_time = strtotime(date('Y-m-d 1:10:00', strtotime('+1000 days', $now)));
        $this->assertTrue($daily_repetition->isActive($test_time));
        list($start, $end) = $daily_repetition->getActiveRange($test_time);

        $expected_start = strtotime('+1000 days', strtotime($daily_repetition->getStartDate()));
        $this->assertEquals(
            date('Y-m-d H:i:s', $expected_start),
            date('Y-m-d H:i:s', $start),
            'Incorrect Start Date');

        $expected_end = strtotime('+1000 days', strtotime($daily_repetition->getEndDate()));
        $this->assertEquals(
            date('Y-m-d H:i:s', $expected_end),
            date('Y-m-d H:i:s', $end),
            'Incorrect End Date');

        unset($daily_repetition);
    }

    public function testWeeklyRecurring()
    {
        $now = strtotime('2/13/2014');

        // Setup
        $weekly_repetition = new Duration();

        $weekly_repetition->setStartDate(date('Y-m-d 1:00:00', $now));
        $weekly_repetition->setEndDate(date('Y-m-d 3:00:00', strtotime('+1 day', $now)));

        $weekly_repetition->setEndDateAllDay(false);
        $weekly_repetition->setStartDateAllDay(false);

        $weekly_weeekday_repetition = clone $weekly_repetition;

        // Same day, repeat on days of the week
        $weekly_weeekday_repetition->setEndDate(
            date('Y-m-d 3:00:00', strtotime($weekly_weeekday_repetition->getStartDate())));
        $weekly_weeekday_repetition->setRepeatPeriod(Duration::REPEAT_WEEKLY);
        $weekly_weeekday_repetition->setRepeatPeriodWeekDays(array(6));

        $weekly_weeekday_repetition->setRepeatEveryNum(1);

        $test_time = strtotime('last saturday of december '.self::getFarYear(3500));
        $test_time = strtotime('3:00:00', $test_time);

        $this->assertTrue($weekly_weeekday_repetition->isActive($test_time));
        $this->assertFalse($weekly_weeekday_repetition->isActive(strtotime('-1 day', $test_time)));

        list($start, $end) = $weekly_weeekday_repetition->getActiveRange($test_time);

        $expected_start = $test_time;
        $this->assertEquals(
            date('Y-m-d 01:00:00', $expected_start),
            date('Y-m-d H:i:s', $start),
            'Incorrect Start Date');

        $this->assertEquals(
            date('Y-m-d 03:00:00', $expected_start),
            date('Y-m-d H:i:s', $end),
            'Incorrect End Date');
        unset($weekly_weeekday_repetition);

        // Multiple day span
        $weekly_repetition->setRepeatPeriod($weekly_repetition::REPEAT_WEEKLY);
        $weekly_repetition->setRepeatEveryNum(1);

        $dow = date('l', $now);
        $test_time = strtotime("last {$dow} of december ".self::getFarYear(3500));
        $test_time = strtotime('3:00:00', $test_time);

        $this->assertTrue($weekly_repetition->isActive($test_time));
        $this->assertFalse($weekly_repetition->isActive(strtotime('-1 day', $test_time)));
    }

    public function testMonthlyRecurring()
    {
        // -- Last day of the week
        $monthly_ldotw_repetition = new Duration();
        $monthly_ldotw_repetition->setStartDate(date('Y-m-d 01:00:00', strtotime('second saturday of december 1992')));
        $monthly_ldotw_repetition->setEndDate(date('Y-m-d 03:00:00', strtotime('second saturday of december 1992')));

        $monthly_ldotw_repetition->setEndDateAllDay(false);
        $monthly_ldotw_repetition->setStartDateAllDay(false);

        $monthly_ldotw_repetition->setRepeatPeriod($monthly_ldotw_repetition::REPEAT_MONTHLY);
        $monthly_ldotw_repetition->setRepeatMonthBy($monthly_ldotw_repetition::MONTHLY_REPEAT_LAST_WEEKDAY);
        $monthly_ldotw_repetition->setRepeatMonthLastWeekday(1); // Monday
        $monthly_ldotw_repetition->setRepeatEveryNum(1);

        $this->assertTrue(
            $monthly_ldotw_repetition->isActive(
                strtotime(date('Y-m-d 01:30:00', strtotime('last monday of march '.self::getFarYear(2205))))));

        // -- Weekly
        $monthly_weekly_repetition = new Duration();
        $monthly_weekly_repetition->setStartDate(date('Y-m-d 01:00:00', strtotime('second saturday of december 1992')));
        $monthly_weekly_repetition->setEndDate(date('Y-m-d 03:00:00', strtotime('second saturday of december 1992')));

        $monthly_weekly_repetition->setEndDateAllDay(false);
        $monthly_weekly_repetition->setStartDateAllDay(false);

        $monthly_weekly_repetition->setRepeatPeriod($monthly_weekly_repetition::REPEAT_MONTHLY);
        $monthly_weekly_repetition->setRepeatMonthBy($monthly_weekly_repetition::MONTHLY_REPEAT_WEEKLY);
        $monthly_weekly_repetition->setRepeatEveryNum(1);

        $this->assertTrue(
            $monthly_weekly_repetition->isActive(
                strtotime(date('Y-m-d 01:30:00', strtotime('second saturday of march '.self::getFarYear(2205))))));

        // -- Monthly
        $monthly_weekly_repetition = new Duration();
        $monthly_weekly_repetition->setStartDate('1990-05-21 01:00:00');
        $monthly_weekly_repetition->setEndDate('1990-05-21 03:00:00');

        $monthly_weekly_repetition->setEndDateAllDay(false);
        $monthly_weekly_repetition->setStartDateAllDay(false);

        $monthly_weekly_repetition->setRepeatPeriod($monthly_weekly_repetition::REPEAT_MONTHLY);
        $monthly_weekly_repetition->setRepeatMonthBy($monthly_weekly_repetition::MONTHLY_REPEAT_MONTHLY);
        $monthly_weekly_repetition->setRepeatEveryNum(1);

        $this->assertTrue($monthly_weekly_repetition->isActive(strtotime(date('Y-m-21 01:50:00', time()))));
    }

    public function testGenerateSingle()
    {
        $now = strtotime('2/13/2014');
        $repetition = new Duration();

        $repetition->setStartDate(date('Y-m-d H:i:s', $now));
        $repetition->setEndDate(date('Y-m-d H:i:s', $now + 86400));

        $occurrences = $repetition->activeRangesBetween($now, strtotime('+5 years', $now));

        $this->assertNotEmpty($occurrences);
        $this->assertEquals(1, count($occurrences));

        $occurrence = $occurrences[0];
        $this->assertEquals(strtotime($repetition->getStartDate()), $occurrence[0]);
        $this->assertEquals(strtotime($repetition->getEndDate()), $occurrence[1]);

        $repetition->setEndDate(null);
        $this->assertTrue($repetition->isActive($now));
    }

    public function testGenerateDaily()
    {
        $repetition = new Duration();

        // Every 2 days
        $repetition->setRepeatPeriod($repetition::REPEAT_DAILY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('12/10/1992 1:00:00');
        $repetition->setEndDate('12/11/1992 1:00:00');

        $now = strtotime('2/13/2014');
        $occurrences = $repetition->activeRangesBetween($now, strtotime('+5 years', $now));

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence[0]);
            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] != $occurrence[0] || $window[1] != $occurrence[1]) {
                $all_active = false;
                break;
            }
        }

        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }

    public function testGenerateWeekly()
    {
        $repetition = new Duration();

        // Every 2 days
        $repetition->setRepeatPeriod($repetition::REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('1/10/2015 01:00:00');
        $repetition->setEndDate('1/10/2015 03:00:00');

        // Sunday, Tuesday
        $repetition->setRepeatPeriodWeekDays(array(2, 3, 0));

        $now = strtotime('2/13/2014');
        $occurrences = $repetition->activeRangesBetween($now, strtotime('+5 years', $now));

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence[0] + 5);

            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] != $occurrence[0] || $window[1] != $occurrence[1]) {
                $all_active = false;
                break;
            }
        }

        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }

    public function testGenerateMonthlyWeekly()
    {
        $repetition = new Duration();

        $repetition->setRepeatPeriod($repetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy($repetition::MONTHLY_REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('2/1/2015 1:00:00');
        $repetition->setEndDate('2/10/2015 3:00:00');

        $now = strtotime('2/13/2014');
        $end = strtotime('+5 years', $now);

        $occurrences = $repetition->activeRangesBetween($now, $end);

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence[0]);

            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] != $occurrence[0] || $window[1] != $occurrence[1]) {
                $all_active = false;
                break;
            }
        }
        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }

    public function testGenerateMonthlyMonthly()
    {
        $repetition = new Duration();

        $repetition->setRepeatPeriod($repetition::REPEAT_MONTHLY);
        $repetition->setRepeatMonthBy($repetition::MONTHLY_REPEAT_MONTHLY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('1/14/2015 1:00:00');
        $repetition->setEndDate('1/14/2015 3:00:00');

        $now = strtotime('2/13/2014');
        $end = strtotime('+5 years', $now);

        $occurrences = $repetition->activeRangesBetween($now, $end);

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence[0]);

            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] != $occurrence[0] || $window[1] != $occurrence[1]) {
                $all_active = false;
                break;
            }
        }
        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }
}
