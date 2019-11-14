<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
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
        $driverCollection = $driverManager->getDriverCollection($event);
        $this->assertInstanceOf(DriverCollection::class, $driverCollection);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(1, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockEventDriver::class, $driver);
        
        $page = M::mock(Page::class);
        $driverCollection = $driverManager->getDriverCollection($page);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(1, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockPageDriver::class, $driver);
    }
    
    public function testAddMatchingCustomDriverWithNoScore()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockCustomDriver::class);
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionTypeHandle')->andReturn('project');
        $driverCollection = $driverManager->getDriverCollection($page);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(2, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockCustomDriver::class, $driver);
        $driver = $drivers[1];
        $this->assertInstanceOf(MockPageDriver::class, $driver);

        $event = M::mock(CalendarEvent::class);
        $driverCollection = $driverManager->getDriverCollection($event);
        $this->assertInstanceOf(DriverCollection::class, $driverCollection);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(1, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockEventDriver::class, $driver);
    }

    public function testAddMatchingCustomDriverScore()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockCustomDriver::class, 50);
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionTypeHandle')->andReturn('project');
        $driverCollection = $driverManager->getDriverCollection($page);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(2, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockPageDriver::class, $driver);
        $driver = $drivers[1];
        $this->assertInstanceOf(MockCustomDriver::class, $driver);
    }

    public function testAddNonMatchingCustomDriverScore()
    {
        $driverManager = new DriverManager();
        $driverManager->register(MockCustomDriver::class, 50);
        $driverManager->register(MockPageDriver::class);
        $driverManager->register(MockEventDriver::class);
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionTypeHandle')->andReturn('foo');
        $driverCollection = $driverManager->getDriverCollection($page);
        $drivers = $driverCollection->getDrivers();
        $this->assertCount(1, $drivers);
        $driver = $drivers[0];
        $this->assertInstanceOf(MockPageDriver::class, $driver);
    }
}
