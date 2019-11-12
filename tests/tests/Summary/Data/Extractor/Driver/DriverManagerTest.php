<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Data\Extractor\Driver\RegisteredDriver;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;

class MockPageDriver implements DriverInterface
{
    
    public function getCategory()
    {
        return 'page';
    }

    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof Page;
    }
    
    public  function extractData($mixed): Collection
    {
        return new Collection();
    }

}

class MockEventDriver implements DriverInterface
{
    public function getCategory()
    {
        return 'calendar_event';
    }

    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof CalendarEvent;
    }

    public  function extractData($mixed): Collection
    {
        return new Collection();
    }

}

class MockCustomDriver implements DriverInterface
{
    public function getCategory()
    {
        return 'page';
    }

    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof Page && $mixed->getCollectionTypeHandle() === 'project';
    }

    public  function extractData($mixed): Collection
    {
        return new Collection();
    }

}


class DriverManagerTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testRegister()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $drivers = $driverManager->getDrivers();
        $this->assertCount(2, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(RegisteredDriver::class, $driver);
        $this->assertEquals('page', $driver->inflateClass()->getCategory());
        $this->assertEquals(MockPageDriver::class, $driver->getDriver());
    }

    public function testGet()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $event = M::mock(CalendarEvent::class);
        $driver = $driverManager->getDriver($event);
        $this->assertInstanceOf(MockEventDriver::class, $driver);
        $page = M::mock(Page::class);
        $driver = $driverManager->getDriver($page);
        $this->assertInstanceOf(MockPageDriver::class, $driver);
    }
    
    public function testGetWithIncorrectScoreShouldNotOverride()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $driverManager->register(MockCustomDriver::class);
        $page = M::mock(Page::class);
        $driver = $driverManager->getDriver($page);
        $this->assertInstanceOf(MockPageDriver::class, $driver);
    }

    public function testGetWithHighScoreToOverrideAndProperPageType()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $driverManager->register(MockCustomDriver::class, 50);
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionTypeHandle')->andReturn('project');
        $driver = $driverManager->getDriver($page);
        $this->assertInstanceOf(MockCustomDriver::class, $driver);
    }

    public function testOverrideWithProperScoreButNonMatchingPageType()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $driverManager->register(MockCustomDriver::class, 50);
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionTypeHandle')->andReturn('foo');
        $driver = $driverManager->getDriver($page);
        $this->assertInstanceOf(MockPageDriver::class, $driver);
    }
    
}
