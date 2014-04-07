<?php
class DateHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateHelper
     */
    protected $object;
    

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = Loader::helper('date');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testTimeSince() {
        Localization::changeLocale("en_US");
        $minutes = 60;
        $hours = $minutes * 60;
        $days = $hours * 24;
        
        // time is in the future
        $future = time()+ 7;
        $this->assertEquals($this->object->date(DATE_APP_GENERIC_MDY,$future), $this->object->timeSince($future));
        
        // time is now
        $this->assertEquals("0 seconds", $this->object->timeSince(time()));
        
        // time is in the past
        $this->assertEquals("7 seconds",
                            $this->object->timeSince(time() - 7));
        $this->assertEquals("3 minutes",
                            $this->object->timeSince(time() - (3 * $minutes + 13)));
        $this->assertEquals("3 minutes, 13 seconds",
                            $this->object->timeSince(time() - (3 * $minutes + 13),1));
        $this->assertEquals("4 hours",
                            $this->object->timeSince(time() - (4 * $hours + 2 * $minutes)));
        $this->assertEquals("4 hours, 1 minute",
                            $this->object->timeSince(time() - (4 * $hours + 1 * $minutes),1));
        $this->assertEquals("1 day",
                            $this->object->timeSince(time() - (1 * $days + 1 * $minutes)));
        $this->assertEquals("2 days, 2 hours",
                            $this->object->timeSince(time() - (2 * $days + 2 * $hours),1));
        $this->assertEquals('145 days',
                            $this->object->timeSince(time() - (145 * $days)));
        $this->assertEquals($this->object->date(DATE_APP_GENERIC_MDY,(time() - (367 * $days))),
                            $this->object->timeSince(time() - (367 * $days)));
    }
}
