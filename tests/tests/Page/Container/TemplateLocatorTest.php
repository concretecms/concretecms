<?php

namespace Concrete\Tests\Page\Container;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Container\TemplateLocator;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\TestCase;

class TemplateLocatorTest extends TestCase
{
    protected $app;

    /**
     * @var FileLocator
     */
    protected $locator;

    public function setUp(): void
    {
        $this->app = Facade::getFacadeApplication();
        $this->locator = $this->app->make(FileLocator::class);
    }

    protected function createTheme(): Theme
    {
        $theme = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();
        $theme->expects($this->any())
            ->method('getThemeHandle')
            ->will($this->returnValue('elemental'));
        $theme->expects($this->any())
            ->method('getPackageHandle')
            ->will($this->returnValue(null));

        return $theme;
    }

    protected function createPage(Theme $theme): Page
    {
        $page = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects($this->any())
            ->method('getCollectionThemeObject')
            ->will($this->returnValue($theme));

        return $page;
    }

    public function testContainerNotFoundWithoutPackage()
    {
        $theme = $this->createTheme();
        $page = $this->createPage($theme);

        $container = new Container();
        $container->setContainerHandle('missing_container');

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->any())
            ->method('exists')
            ->will($this->returnValue(false));

        $this->locator->setFilesystem($filesystem);

        $templateLocator = new TemplateLocator($this->locator, new FileLocator\ThemeLocation($theme));
        $file = $templateLocator->getFileToRender($page, $container);
        $this->assertNull($file);
    }

    public function testContainerFromPackageIsFound()
    {
        $theme = $this->createTheme();
        $page = $this->createPage($theme);

        $container = $this->getMockBuilder(Container::class)
            ->onlyMethods(['getPackageHandle'])
            ->getMock();
        $container->setContainerHandle('sample_container');
        $container->expects($this->any())
            ->method('getPackageHandle')
            ->will($this->returnValue('sample_package'));

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->any())
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/' . DIRNAME_CONTAINERS . '/sample_container.php', false],
                [DIR_BASE_CORE . '/' . DIRNAME_THEMES . '/elemental/' . DIRNAME_ELEMENTS . '/' . DIRNAME_CONTAINERS . '/sample_container.php', false],
                [DIR_PACKAGES . '/sample_package/' . DIRNAME_ELEMENTS . '/' . DIRNAME_CONTAINERS . '/sample_container.php', true],
            ]));

        $this->locator->setFilesystem($filesystem);

        $templateLocator = new TemplateLocator($this->locator, new FileLocator\ThemeLocation($theme));
        $file = $templateLocator->getFileToRender($page, $container);

        $this->assertEquals(
            DIR_PACKAGES . '/sample_package/' . DIRNAME_ELEMENTS . '/' . DIRNAME_CONTAINERS . '/sample_container.php',
            $file
        );
    }
}