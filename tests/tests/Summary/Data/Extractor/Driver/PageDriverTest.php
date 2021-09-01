<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\InstallationService;
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
    
    public function testIsValidForObject()
    {
        $installationService = M::mock(InstallationService::class);
        $event = M::mock(CalendarEvent::class);
        $page = M::mock(Page::class);
        $driver = new BasicPageDriver($installationService);
        $this->assertFalse($driver->isValidForObject($event));
        $this->assertTrue($driver->isValidForObject($page));
    }
    
    public function testExtractData()
    {
        $installationService = M::mock(InstallationService::class);
        $installationService->shouldReceive('isMultisiteEnabled');
        $file = M::mock(File::class);
        $file->shouldReceive('getFileID')->andReturn(4);

        $page = M::mock(Page::class);
        $page->shouldReceive('getCollectionLink')->once()->andReturn('https://www.foo.com/path/to/page');
        $page->shouldReceive('getCollectionName')->once()->andReturn('My Name');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('');
        $page->shouldReceive('getCollectionDatePublicObject')->once()->andReturn(new \DateTime(
            '2010-01-01 00:00:00', new \DateTimeZone('GMT')
        ));
        $page->shouldReceive('getAttribute')->with('thumbnail')->once()->andReturn($file);
        $driver = new BasicPageDriver($installationService);
        $data = $driver->extractData($page);
        $this->assertInstanceOf(Collection::class, $data);
        $fields = $data->getFields();
        $this->assertCount(4, $fields);
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
        $collection = $serializer->deserialize($data, Collection::class, 'json');
        $fields = $collection->getFields();
        $this->assertCount(4, $fields);
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldDataInterface::class, $field);
        $this->assertEquals('My Name', $field);
        $field = $collection->getField(FieldInterface::FIELD_THUMBNAIL);
        $this->assertInstanceOf(DataFieldDataInterface::class, $field);
    }
}
