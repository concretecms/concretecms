<?php

namespace Concrete\Tests\Url\Resolver;

use Concrete\TestHelpers\CreateClassMockTrait;
use Concrete\TestHelpers\Url\Resolver\ResolverTestCase;

class PageUrlResolverTest extends ResolverTestCase
{
    use CreateClassMockTrait;

    protected function setUp()
    {
        parent::setUp();
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->urlResolver = $app->make('Concrete\Core\Url\Resolver\PageUrlResolver');
    }

    public function testWithPage()
    {
        $path = '/some/collection/path';
        $page = $this->createMockFromClass('\Concrete\Core\Page\Page');
        $page->expects($this->once())
                ->method('getCollectionPath')
                ->willReturn($path);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath($path),
            (string) $this->urlResolver->resolve([$page]));
    }

    public function testWithHome()
    {
        $homePageID = 12345;
        $page = $this->createMockFromClass('\Concrete\Core\Page\Page');
        $page->expects($this->once())
            ->method('getCollectionID')
            ->willReturn($homePageID);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath('/')->setQuery('cID=' . $homePageID),
            (string) $this->urlResolver->resolve([$page]));
    }

    public function testUnapproved()
    {
        $page = $this->createMockFromClass('\Concrete\Core\Page\Page');
        $page->expects($this->once())
            ->method('getCollectionID')
            ->willReturn(1337);

        $this->assertEquals(
            (string) $this->canonicalUrlWithPath('/')->setQuery('cID=1337'),
            (string) $this->urlResolver->resolve([$page]));
    }

    public function testAlreadyResolved()
    {
        $path = '/some/collection/path';
        $page = $this->createMockFromClass('\Concrete\Core\Page\Page');
        $page->expects($this->never())
            ->method('getCollectionPath')
            ->willReturn($path);

        $this->assertEquals($this, $this->urlResolver->resolve([$page], $this));
    }

    public function testEmptyArguments()
    {
        $this->assertNull($this->urlResolver->resolve([]));
    }
}
