<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Extractor\Driver\PageThumbnailDriver;
use Concrete\Core\Summary\Data\Field\DataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
class DriverCollectionTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testExtractData()
    {
        $driverCollection = new DriverCollection();
        $page = M::Mock(Page::class);
        $page->shouldReceive('getCollectionName')->andReturn('hi');
        $page->shouldReceive('getCollectionLink')->andReturn('https://foo.com');
        $page->shouldReceive('getCollectionDescription')->andReturn('');
        $driver = M::mock(BasicPageDriver::class)->makePartial();
        $driverCollection->addDriver($driver);
        $driverCollection->addDriver($driver); // let's make sure we don't add multiple fields to the array
        $collection = $driverCollection->extractData($page);
        $this->assertCount(2, $collection->getFields());
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('hi', $field);
    }

    public function testExtractDataWithThumbnail()
    {
        $driverCollection = new DriverCollection();
        $page = M::Mock(Page::class);
        $file = M::Mock(File::class);
        $file->shouldReceive('getFileID')->andReturn(3);
        $page->shouldReceive('getCollectionName')->once()->andReturn('hi');
        $page->shouldReceive('getCollectionLink')->once()->andReturn('https://foo.com');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('asd');
        $page->shouldReceive('getAttribute')->with('thumbnail')->once()->andReturn($file);
        $driver = M::mock(BasicPageDriver::class)->makePartial();
        $driver2 = M::mock(PageThumbnailDriver::class)->makePartial();
        $driverCollection->addDriver($driver);
        $driverCollection->addDriver($driver2);
        $collection = $driverCollection->extractData($page);
        $this->assertCount(4, $collection->getFields());
        $field = $collection->getField(FieldInterface::FIELD_DESCRIPTION);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('asd', $field);
        $field = $collection->getField(FieldInterface::FIELD_THUMBNAIL);
        $this->assertInstanceOf(ImageDataFieldData::class, $field);
    }



}
