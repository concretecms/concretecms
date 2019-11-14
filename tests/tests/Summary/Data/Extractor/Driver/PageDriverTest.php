<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\PageThumbnailDriver;
use Concrete\Core\Summary\Data\Field\DataFieldDataInterface;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;


class PageDriverTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testIsValidForObject()
    {
        $event = M::mock(CalendarEvent::class);
        $page = M::mock(Page::class);
        $driver = new BasicPageDriver();
        $this->assertFalse($driver->isValidForObject($event));
        $this->assertTrue($driver->isValidForObject($page));
    }
    
    public function testExtractData()
    {
        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionLink')->once()->andReturn('https://www.foo.com/path/to/page');
        $page->shouldReceive('getCollectionName')->once()->andReturn('My Name');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('');
        $driver = new BasicPageDriver();
        $data = $driver->extractData($page);
        $this->assertInstanceOf(Collection::class, $data);
        $fields = $data->getFields();
        $this->assertCount(2, $fields);
        $field = $data->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldDataInterface::class, $field);
        $this->assertEquals('My Name', $field); 
        
        $serializer = new Serializer([
            new JsonSerializableNormalizer(),
            new CustomNormalizer()
        ], [
            new JsonEncoder()
        ]);
        $data = $serializer->serialize($data, 'json');
        $this->assertEquals(
            '{"fields":{"title":{"class":"Concrete\\\Core\\\Summary\\\Data\\\Field\\\DataFieldData","data":"My Name"},"link":{"class":"Concrete\\\Core\\\Summary\\\Data\\\Field\\\DataFieldData","data":"https:\/\/www.foo.com\/path\/to\/page"}}}',
            $data
        );
        
        $collection = $serializer->deserialize($data, Collection::class, 'json');
        $fields = $collection->getFields();
        $this->assertCount(2, $fields);
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldDataInterface::class, $field);
        $this->assertEquals('My Name', $field);
    }

    public function testExtractThumbnail()
    {
        $page = M::mock(Page::class);
        $file = M::mock(File::class);
        $file->shouldReceive('getFileID')->andReturn(4);
        $page->shouldReceive('getAttribute')->with('thumbnail')->once()->andReturn($file);
        $driver = new PageThumbnailDriver();
        $data = $driver->extractData($page);
        $this->assertInstanceOf(Collection::class, $data);
        $fields = $data->getFields();
        $this->assertCount(1, $fields);
        $field = $data->getField(FieldInterface::FIELD_THUMBNAIL);
        $this->assertInstanceOf(DataFieldDataInterface::class, $field);
    }




}
