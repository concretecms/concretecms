<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\PageDriver;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;


class PageDriverTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testIsValidForObject()
    {
        $event = M::mock(CalendarEvent::class);
        $page = M::mock(Page::class);
        $driver = new PageDriver();
        $this->assertFalse($driver->isValidForObject($event));
        $this->assertTrue($driver->isValidForObject($page));
    }
    
    public function testExtractData()
    {
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionLink')->once()->andReturn('https://www.foo.com/path/to/page');
        $page->shouldReceive('getCollectionName')->once()->andReturn('My Name');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('');
        $driver = new PageDriver();
        $data = $driver->extractData($page);
        $this->assertInstanceOf(Collection::class, $data);
        $fields = $data->getFields();
        $this->assertCount(2, $fields);
        $data = $data->getField(FieldInterface::FIELD_TITLE);
        $this->assertEquals('My Name', $data); 
    }

    
    
}
