<?php

namespace Concrete\Tests\Filesystem;

use Concrete\Core\Entity\Package;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Support\Facade\Facade;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

class FileLocatorTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    /**
     * @var FileLocator
     */
    protected $locator;

    public function setUp()
    {
        $this->app = Facade::getFacadeApplication();
        $this->locator = $this->app->make(FileLocator::class);
    }

    public function testBasicLocate()
    {
        $record = $this->locator->getRecord(DIRNAME_ATTRIBUTES . '/social_links/view.css');
        $this->assertInstanceOf('Concrete\Core\Filesystem\FileLocator\Record', $record);
        $this->assertEquals(DIR_BASE_CORE . '/attributes/social_links/view.css', $record->getFile());
        $this->assertEquals('/path/to/server/concrete/attributes/social_links/view.css', $record->getUrl());
        $this->assertTrue($record->exists());
    }

    public function testBasicLocateNotExists()
    {
        $record = $this->locator->getRecord(DIRNAME_BLOCKS . '/rss_displayer/templates/fancy/view.php');
        $this->assertInstanceOf('Concrete\Core\Filesystem\FileLocator\Record', $record);
        $this->assertEquals(DIR_BASE_CORE . '/blocks/rss_displayer/templates/fancy/view.php', $record->getFile());
        $this->assertEquals('/path/to/server/concrete/blocks/rss_displayer/templates/fancy/view.php', $record->getUrl());
        $this->assertFalse($record->exists());
    }

    public function testPackageLocate()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(2))
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/' . DIRNAME_ELEMENTS . '/awesome/thing.php', false],
                [DIR_PACKAGES . '/awesome/' . DIRNAME_ELEMENTS . '/awesome/thing.php', true],
            ]));

        $this->locator->setFilesystem($filesystem);
        $this->locator->addPackageLocation('awesome');
        $record = $this->locator->getRecord(DIRNAME_ELEMENTS . '/awesome/thing.php');
        $this->assertTrue($record->exists());
        $this->assertEquals(DIR_PACKAGES . '/awesome/elements/awesome/thing.php', $record->getFile());
        $this->assertEquals('/path/to/server/packages/awesome/elements/awesome/thing.php', $record->getUrl());
    }

    public function testOverride()
    {
        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->once())
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/blocks/autonav/view.php', true],
            ]));

        $this->locator->setFilesystem($filesystem);
        $this->locator->addPackageLocation('awesome');
        $record = $this->locator->getRecord(DIRNAME_BLOCKS . '/autonav/view.php');
        $this->assertTrue($record->exists());
        $this->assertTrue($record->isOverride());
        $this->assertEquals(DIR_BASE . '/application/blocks/autonav/view.php', $record->getFile());
        $this->assertEquals('/path/to/server/application/blocks/autonav/view.php', $record->getUrl());
    }

    public function testCheckAllPackages()
    {
        // First, we create the package list we're going to use. It's going to have three mock packages in it
        $packages = [];
        foreach (['calendar', 'thumbnails_pro', 'superfish'] as $pkgHandle) {
            $pkg = new Package();
            $pkg->setPackageHandle($pkgHandle);
            $packages[] = $pkg;
        }
        $packageList = $this->getMockBuilder(PackageList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageList->expects($this->any())
            ->method('getPackages')
            ->will($this->returnValue($packages));

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(3))
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/blocks/page_list/templates/fancy_thumbnails/view.php', false],
                [DIR_PACKAGES . '/calendar/blocks/page_list/templates/fancy_thumbnails/view.php', false],
                [DIR_PACKAGES . '/thumbnails_pro/blocks/page_list/templates/fancy_thumbnails/view.php', true],
            ]));

        $this->locator->setFilesystem($filesystem);
        $this->locator->addLocation(new FileLocator\AllPackagesLocation($packageList, $filesystem));

        $record = $this->locator->getRecord(DIRNAME_BLOCKS . '/page_list/templates/fancy_thumbnails/view.php');
        $this->assertEquals(DIR_BASE . '/packages/thumbnails_pro/blocks/page_list/templates/fancy_thumbnails/view.php', $record->getFile());
        $this->assertEquals('/path/to/server/packages/thumbnails_pro/blocks/page_list/templates/fancy_thumbnails/view.php', $record->getUrl());
    }

    public function testCurrentThemeElemental()
    {
        $theme = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();

        $theme->expects($this->any())
            ->method('getThemeHandle')
            ->will($this->returnValue('elemental'));
        $theme->expects($this->once())
            ->method('getPackageHandle')
            ->will($this->returnValue(null));

        $this->locator->addLocation(new FileLocator\ThemeLocation($theme));
        $record = $this->locator->getRecord(DIRNAME_ELEMENTS . '/header_required.php');
        $this->assertEquals(DIR_BASE_CORE . '/elements/header_required.php', $record->getFile());
        $this->assertEquals('/path/to/server/concrete/elements/header_required.php', $record->getUrl());
    }

    public function testBlockCustomTemplateInATheme()
    {
        $theme = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();

        $theme->expects($this->any())
            ->method('getThemeHandle')
            ->will($this->returnValue('brilliant'));
        $theme->expects($this->once())
            ->method('getPackageHandle')
            ->will($this->returnValue('brilliant_theme'));

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(2))
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/blocks/page_list/templates/fancy_list.php', false],
                [DIR_PACKAGES . '/brilliant_theme/themes/brilliant/blocks/page_list/templates/fancy_list.php', true],
            ]));

        $this->locator->addLocation(new FileLocator\ThemeLocation($theme));
        $this->locator->setFilesystem($filesystem);
        $record = $this->locator->getRecord(DIRNAME_BLOCKS . '/page_list/templates/fancy_list.php');
        $this->assertEquals(DIR_BASE . '/packages/brilliant_theme/themes/brilliant/blocks/page_list/templates/fancy_list.php', $record->getFile());
        $this->assertEquals('/path/to/server/packages/brilliant_theme/themes/brilliant/blocks/page_list/templates/fancy_list.php', $record->getUrl());
        $this->assertTrue($record->exists());
    }

    public function testOverridingConversationElementInPackagedTheme()
    {
        $theme = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();

        $theme->expects($this->any())
            ->method('getThemeHandle')
            ->will($this->returnValue('brilliant'));
        $theme->expects($this->once())
            ->method('getPackageHandle')
            ->will($this->returnValue('brilliant_theme'));

        $filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->exactly(2))
            ->method('exists')
            ->will($this->returnValueMap([
                [DIR_APPLICATION . '/elements/conversation/display.php', false],
                [DIR_PACKAGES . '/brilliant_theme/themes/brilliant/elements/concrete/conversation/display.php', true],
            ]));

        $this->locator->addLocation(new FileLocator\ThemeElementLocation($theme));
        $this->locator->setFilesystem($filesystem);
        $record = $this->locator->getRecord(DIRNAME_ELEMENTS . '/conversation/display.php');
        $this->assertEquals(DIR_BASE . '/packages/brilliant_theme/themes/brilliant/elements/concrete/conversation/display.php', $record->getFile());
        $this->assertEquals('/path/to/server/packages/brilliant_theme/themes/brilliant/elements/concrete/conversation/display.php', $record->getUrl());
        $this->assertTrue($record->exists());
    }
}
