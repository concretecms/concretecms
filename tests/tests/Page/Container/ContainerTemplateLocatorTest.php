<?php

namespace Concrete\Tests\Page\Container;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Page\Container\TemplateLocator;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use Concrete\Core\Filesystem\FileLocator\ThemeLocation;

class ContainerTemplateLocatorTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;

    public function testGetFileToRender()
    {
        $container = M::mock(Container::class);
        $theme = M::mock(Theme::class);
        $page = M::mock(Page::class);
        $theme->shouldReceive('getPackageHandle')->andReturn(null);
        $theme->shouldReceive('getThemeHandle')->andReturn('elemental');
        $page->shouldReceive('getCollectionThemeObject')->andReturn($theme);
        $container->shouldReceive('getContainerTemplateFile')->andReturn('fart.php');
        $locator = M::mock(FileLocator::class);
        $locator->shouldReceive('getRecord');

        $themeLocation = M::mock(ThemeLocation::class);
        $themeLocation->shouldReceive('setTheme')->with($theme);
        $locator->shouldReceive('getLocator');        
        $locator->shouldReceive('addLocation')->withArgs([$themeLocation]);
        $locator = new TemplateLocator($locator, $themeLocation);
        $locator->getFileToRender($page, $container);
    }

}
