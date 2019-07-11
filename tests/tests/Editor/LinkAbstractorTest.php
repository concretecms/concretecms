<?php

namespace Concrete\Tests\Editor;

use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Support\Facade\Facade;
use Core;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit_Framework_TestCase;

class LinkAbstractorTest extends PHPUnit_Framework_TestCase
{
    public function testTranslateFromEditMode()
    {
        $baseUrl = 'http://www.dummyco.com/path/to/server';

        $input = '<a href="{CCM:BASE_URL}/test">Link</a>';
        $this->assertEquals(
            '<a href="' . $baseUrl . '/test">Link</a>',
            LinkAbstractor::translateFromEditMode($input)
        );

        $input = '<a href="{CCM:CID_123}">Link</a>';
        $this->assertEquals(
            '<a href="' . $baseUrl . '/index.php?cID=123">Link</a>',
            LinkAbstractor::translateFromEditMode($input)
        );

        $input = '<concrete-picture fID="123" />';
        $this->assertEquals(
            '<img src="' . $baseUrl . '/index.php/download_file/view_inline/123" />',
            LinkAbstractor::translateFromEditMode($input)
        );

        $input = '<a href="{CCM:FID_123}">Link</a>';
        $this->assertEquals(
            '<a href="' . $baseUrl . '/index.php/download_file/view_inline/123">Link</a>',
            LinkAbstractor::translateFromEditMode($input)
        );

        $input = '<a href="{CCM:FID_DL_123}">Link</a>';
        $this->assertEquals(
            '<a href="' . $baseUrl . '/index.php/download_file/view/123">Link</a>',
            LinkAbstractor::translateFromEditMode($input)
        );
    }

    public function testTranslateTo()
    {
        $baseUrl = 'http://www.dummyco.com/path/to/server';

        $input = '<a href="' . $baseUrl . '/test">Link</a>';
        $this->assertEquals(
            '<a href="{CCM:BASE_URL}/test">Link</a>',
            LinkAbstractor::translateTo($input)
        );

        $input = '<a href="' . $baseUrl . '/index.php?cID=123">Link</a>';
        $this->assertEquals(
            '<a href="{CCM:CID_123}">Link</a>',
            LinkAbstractor::translateTo($input)
        );

        $input = '<img src="' . $baseUrl . '/index.php/download_file/view_inline/123" />';
        $this->assertEquals(
            '<concrete-picture fID="123" />',
            LinkAbstractor::translateTo($input)
        );

        $input = '<a href="' . $baseUrl . '/index.php/download_file/view/123">Link</a>';
        $this->assertEquals(
            '<a href="{CCM:FID_DL_123}">Link</a>',
            LinkAbstractor::translateTo($input)
        );
    }

    public function testExport()
    {
        $app = Facade::getFacadeApplication();

        // Mock the cache class so that we can return a mocked page object
        // without initializing the page related database tables.
        $origCache = Core::make('cache/request');
        $cache = $this->getMockBuilder('Concrete\Core\Cache\Level\RequestCache')
            ->disableOriginalConstructor()
            ->getMock();
        $app->bind('cache/request', function () use ($cache) {
            return $cache;
        });

        // Make the local request cache enabled always.
        $cache->expects($this->any())->method('isEnabled')->willReturn(true);

        // Mock the entity manager so that we can return a mocked file object
        // without initializing the files related database tables.
        $origEm = Core::make(EntityManagerInterface::class);
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $app->bind(EntityManagerInterface::class, function () use ($em) {
            return $em;
        });

        // Create a mock page object and corresponding cache item to return by
        // the mocked cache class.
        $mockPage = $this->getMockBuilder('Concrete\Core\Page\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $mockPage->expects($this->once())
            ->method('getCollectionPath')
            ->willReturn('/test/page/path');
        $pageCacheItem = $this->getMockBuilder('Stash\Item')
            ->disableOriginalConstructor()
            ->getMock();
        $pageCacheItem->expects($this->once())
            ->method('isMiss')
            ->willReturn(false);
        $pageCacheItem->expects($this->once())
            ->method('get')
            ->willReturn($mockPage);

        // Bind the cache getEntry method to return the corresponding page
        // cache item.
        $cache->expects($this->once())
            ->method('getItem')
            ->with(
                CacheLocal::key('page', '123/RECENT/Concrete\Core\Page\Page')
            )
            ->willReturn($pageCacheItem);

        // Test that the link abstractor exports the page correctly
        $input = '<a href="{CCM:CID_123}">Link</a>';
        $this->assertEquals(
            '<a href="{ccm:export:page:/test/page/path}">Link</a>',
            LinkAbstractor::export($input)
        );

        // Create a mock file object and make the mocked entity manager return
        // that.
        $mockFile = $this->getMockBuilder('Concrete\Core\Entity\File\File')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFile->expects($this->any())
            ->method('__call')
            ->will($this->returnCallback(function ($method, $args) {
                if ($method === 'getPrefix') {
                    return '123456789012';
                } elseif ($method === 'getFileName' ||
                    $method === 'getFilename'
                ) {
                    return 'test_file.jpg';
                }

                return null;
            }));

        $em->expects($this->exactly(2))
            ->method('find')
            ->with(
                $this->equalTo('Concrete\Core\Entity\File\File'),
                $this->equalTo(123)
            )
            ->willReturn($mockFile);

        // Test that the link abstractor exports the file correctly
        $input = '<a href="{CCM:FID_DL_123}">Link</a>';
        $this->assertEquals(
            '<a href="{ccm:export:file:123456789012:test_file.jpg}">Link</a>',
            LinkAbstractor::export($input)
        );

        // The that the link abstractor exports the images correctly
        $input = '<concrete-picture fID="123" />';
        $this->assertEquals(
            '<concrete-picture file="123456789012:test_file.jpg" />',
            LinkAbstractor::export($input)
        );

        // Revert to the defaults
        $app->bind('cache/request', function () use ($origCache) {
            return $origCache;
        });
        $app->bind(EntityManagerInterface::class, function () use ($origEm) {
            return $origEm;
        });
    }
}
