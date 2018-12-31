<?php
namespace Concrete\Tests\Localization\Service;

use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Localization\Translator\Adapter\Plain\TranslatorAdapterFactory;
use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;
use Concrete\Core\Support\Facade\Facade;

/**
 * Tests for:
 * Concrete\Core\Localization\Service\Date.
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /** @var Localization */
    protected $localization;
    /** @var Liaison */
    protected $config;
    protected $serverTimezone;

    public function testDescribeInterval()
    {
        $date = new Date();

        $diff = 30;
        $this->assertEquals('30 seconds', $date->describeInterval($diff));
        $diff = 60 * 3 + 30;
        $this->assertEquals('3 minutes', $date->describeInterval($diff));
        $this->assertEquals('3 minutes and 30 seconds', $date->describeInterval($diff, true));
        $diff = (60 * 60 * 3) + (60 * 30);
        $this->assertEquals('3 hours', $date->describeInterval($diff));
        $this->assertEquals('3 hours and 30 minutes', $date->describeInterval($diff, true));
        $diff = (60 * 60 * 24 * 3) + (60 * 60 * 3);
        $this->assertEquals('3 days', $date->describeInterval($diff));
        $this->assertEquals('3 days and 3 hours', $date->describeInterval($diff, true));
    }

    /**
     * @throws \Exception
     */
    public function testFormatCustom()
    {
        $date = new Date();
        $timeZone = new \DateTimeZone('UTC');
        $dateTime = new \DateTime('2018-12-31 21:00:00', $timeZone);
        $this->assertEquals('2018-12-31T21:00:00+00:00', $date->formatCustom(\DateTime::ATOM, $dateTime, 'UTC'));
        $this->assertEquals('2018-12-31', $date->formatCustom('Y-m-d', $dateTime, 'UTC'));
    }

    /**
     * @throws \Punic\Exception
     */
    public function testFormatDate()
    {
        $date = new Date();
        $timeZone = new \DateTimeZone('UTC');
        $dateTime = new \DateTime('2018-12-31 21:00:00', $timeZone);
        $this->assertEquals('12/31/18', $date->formatDate($dateTime, 'short', 'UTC'));
        $this->assertEquals('12/31/18', $date->formatDate($dateTime, false, 'UTC')); // For backward compatibility
    }

    /**
     * @throws \Exception
     */
    public function testFormatDateTime()
    {
        $date = new Date();
        $timeZone = new \DateTimeZone('UTC');
        $dateTime = new \DateTime('2018-12-31 21:00:00', $timeZone);
        $this->assertEquals('12/31/18, 9:00 PM', $date->formatDateTime($dateTime, false, false, 'UTC'));
        $this->assertEquals('Dec 31, 2018, 9:00 PM', $date->formatDateTime($dateTime, true, false, 'UTC'));
        $this->assertEquals('12/31/18, 9:00:00 PM', $date->formatDateTime($dateTime, false, true, 'UTC'));
        $this->assertEquals('Dec 31, 2018, 9:00:00 PM', $date->formatDateTime($dateTime, true, true, 'UTC'));
    }

    /**
     * @throws \Exception
     */
    public function testFormatTime()
    {
        $date = new Date();
        $timeZone = new \DateTimeZone('UTC');
        $dateTime = new \DateTime('2018-12-31 21:00:00', $timeZone);
        $this->assertEquals('9:00 PM', $date->formatTime($dateTime, false, 'UTC'));
        $this->assertEquals('9:00:00 PM', $date->formatTime($dateTime, true, 'UTC'));
    }

    public function testGetDeltaDays()
    {
        $date = new Date();
        $from = '2018-12-30 12:00:00';
        $morningToday = '2018-12-30 06:00:00';
        $eveningToday = '2018-12-30 18:00:00';
        $morningTomorrow = '2018-12-31 06:00:00';
        $eveningTomorrow = '2018-12-31 18:00:00';
        $morningYesterday = '2018-12-29 06:00:00';
        $eveningYesterday = '2018-12-29 18:00:00';

        $this->config->set('app.server_timezone', 'UTC');
        $this->assertEquals(0, $date->getDeltaDays($from, $morningToday, 'system'));
        $this->assertEquals(0, $date->getDeltaDays($from, $eveningToday, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $morningTomorrow, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $eveningTomorrow, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $morningYesterday, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $eveningYesterday, 'system'));

        $this->config->set('app.server_timezone', 'PST');
        $this->assertEquals(0, $date->getDeltaDays($from, $morningToday, 'system'));
        $this->assertEquals(0, $date->getDeltaDays($from, $eveningToday, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $morningTomorrow, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $eveningTomorrow, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $morningYesterday, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $eveningYesterday, 'system'));

        $this->config->set('app.server_timezone', 'JST');
        $this->assertEquals(0, $date->getDeltaDays($from, $morningToday, 'system'));
        $this->assertEquals(0, $date->getDeltaDays($from, $eveningToday, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $morningTomorrow, 'system'));
        $this->assertEquals(1, $date->getDeltaDays($from, $eveningTomorrow, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $morningYesterday, 'system'));
        $this->assertEquals(-1, $date->getDeltaDays($from, $eveningYesterday, 'system'));

        $this->config->set('app.server_timezone', 'UTC');
    }

    public function testGetTimeFormat()
    {
        $date = new Date();

        // Should return 12 when the current locale is en_US
        $this->assertEquals(12, $date->getTimeFormat());

        // Change active context
        $this->localization->pushActiveContext('24hour');

        // Should return 24 when the current locale is ja_JP
        $this->assertEquals(24, $date->getTimeFormat());

        // Restore the context
        $this->localization->popActiveContext();
    }

    /**
     * @return array
     */
    public function getTimezoneDataProvider()
    {
        return [
            ['system', new \DateTimeZone('UTC')],
            ['PST', new \DateTimeZone('PST')],
            ['ABC', null],
        ];
    }

    /**
     * @dataProvider getTimezoneDataProvider
     */
    public function testGetTimezone($timezone, $expected)
    {
        $date = new Date();
        $this->assertEquals($expected, $date->getTimezone($timezone));
    }

    protected function setUp()
    {
        $this->localization = new Localization();
        $translatorAdapterFactory = new TranslatorAdapterFactory();
        $repository = new TranslatorAdapterRepository($translatorAdapterFactory);
        $this->localization->setTranslatorAdapterRepository($repository);
        $this->localization->setActiveContext(Localization::CONTEXT_UI);
        $this->localization->setContextLocale('24hour', 'ja_JP');

        $app = Facade::getFacadeApplication();
        $this->config = $app->make('config');
        $this->serverTimezone = $this->config->get('app.server_timezone');
        $this->config->set('app.server_timezone', 'UTC');
    }

    protected function tearDown()
    {
        $this->localization->setActiveContext(Localization::CONTEXT_SYSTEM);
        $this->config->set('app.server_timezone', $this->serverTimezone);
    }
}
