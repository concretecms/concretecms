<?php

namespace Concrete\Tests\Foundation;

use Concrete\Core\Foundation\Repetition\BasicRepetition;
use Concrete\Core\Foundation\Repetition\Comparator;
use PHPUnit_Framework_TestCase;

class RepetitionComparatorTest extends PHPUnit_Framework_TestCase
{
    public function testNoRepeat()
    {
        $repetition = new BasicRepetition();
        $repetition->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $repetition->setStartDate('2017-08-01 12:00:00');

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r2->setStartDate('2017-08-01 12:00:00');

        $comparator = new Comparator();
        $this->assertTrue($comparator->areEqual($repetition, $r2));

        $r2->setStartDateAllDay(true);
        $this->assertFalse($comparator->areEqual($repetition, $r2));

        $r2->setStartDateAllDay(false);
        $this->assertTrue($comparator->areEqual($repetition, $r2));

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r2->setStartDate('2017-08-01 12:00:01');

        $this->assertFalse($comparator->areEqual($repetition, $r2));

        $repetition->setStartDate('2017-08-01 12:00:01');
        $repetition->setEndDate('2017-08-02 22:00:00');
        $r2->setEndDate('2017-08-02 22:00:00');

        $this->assertTrue($comparator->areEqual($repetition, $r2));

        $repetition = new BasicRepetition();
        $repetition->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $repetition->setStartDate('2017-08-01 12:00:00');

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/New_York'));
        $r2->setStartDate('2017-08-01 12:00:00');

        $this->assertFalse($comparator->areEqual($repetition, $r2));
    }

    public function testRepeatDaily()
    {
        $r1 = new BasicRepetition();
        $r1->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r1->setStartDate('2017-09-12 12:00:00');
        $r1->setEndDate('2017-09-12 14:00:00');
        $r1->setRepeatPeriod(BasicRepetition::REPEAT_DAILY);
        $r1->setRepeatEveryNum(1);

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r2->setStartDate('2017-09-12 12:00:00');
        $r2->setEndDate('2017-09-12 14:00:00');
        $r2->setRepeatPeriod(BasicRepetition::REPEAT_DAILY);
        $r2->setRepeatEveryNum(1);

        $comparator = new Comparator();
        $this->assertTrue($comparator->areEqual($r1, $r2));

        $r2->setRepeatEveryNum(2);
        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r2->setRepeatEveryNum(1);
        $this->assertTrue($comparator->areEqual($r1, $r2));

        $r2->setRepeatPeriodEnd('2017-10-30');
        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r1->setRepeatPeriodEnd('2017-10-30');
        $this->assertTrue($comparator->areEqual($r1, $r2));
    }

    public function testRepeatWeekly()
    {
        $r1 = new BasicRepetition();
        $r1->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r1->setStartDate('2017-09-12 12:00:00');
        $r1->setEndDate('2017-09-12 14:00:00');
        $r1->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $r1->setRepeatEveryNum(1);
        $r1->setRepeatPeriodWeekDays([5]);
        $r1->setRepeatPeriodEnd('2017-11-07');

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r2->setStartDate('2017-09-12 12:00:00');
        $r2->setEndDate('2017-09-12 14:00:00');
        $r2->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $r2->setRepeatEveryNum(1);
        $r2->setRepeatPeriodWeekDays([1, 3]);
        $r2->setRepeatPeriodEnd('2017-11-07');

        $comparator = new Comparator();
        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r1->setRepeatPeriodWeekDays([3, 1]);
        $this->assertTrue($comparator->areEqual($r1, $r2));
    }

    public function testRepeatMonthly()
    {
        $r1 = new BasicRepetition();
        $r1->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r1->setStartDate('2017-09-12 12:00:00');
        $r1->setEndDate('2017-09-12 14:00:00');
        $r1->setRepeatPeriod(BasicRepetition::REPEAT_MONTHLY);
        $r1->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_MONTHLY);
        $r1->setRepeatEveryNum(1);

        $r2 = new BasicRepetition();
        $r2->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $r2->setStartDate('2017-09-12 12:00:00');
        $r2->setEndDate('2017-09-12 14:00:00');
        $r2->setRepeatPeriod(BasicRepetition::REPEAT_MONTHLY);
        $r2->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_MONTHLY);
        $r2->setRepeatEveryNum(1);

        $comparator = new Comparator();
        $this->assertTrue($comparator->areEqual($r1, $r2));

        $r2->setRepeatEveryNum(2);
        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r1->setRepeatEveryNum(2);
        $this->assertTrue($comparator->areEqual($r1, $r2));

        $r2->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r1->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
        $r2->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_WEEKLY);

        $this->assertFalse($comparator->areEqual($r1, $r2));

        $r1->setRepeatMonthBy(BasicRepetition::MONTHLY_REPEAT_WEEKLY);
        $this->assertTrue($comparator->areEqual($r1, $r2));
    }
}
