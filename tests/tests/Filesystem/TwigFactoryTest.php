<?php

namespace Concrete\Tests\Filesystem;

use Concrete\Core\Filesystem\TwigFactory;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Twig\Cache\CacheInterface;
use Twig\Loader\FilesystemLoader;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TwigFactoryTest extends MockeryTestCase
{
    public function testAddNamespaceRegistersCustomTwigNamespaceForDevelopers(): void
    {
        $templateDirectory = '/my/custom/templates';

        Mockery::mock('alias:Concrete\Core\Page\Page')
            ->shouldReceive('getCurrentPage')
            ->andReturn(null);
        Mockery::mock('alias:Concrete\Core\Page\Theme\Theme')
            ->shouldReceive('getSiteTheme')
            ->andReturn(null);

        $factory = $this->createFactory();
        $factory->addNamespace($templateDirectory, 'my_package');

        $loader = Mockery::mock(FilesystemLoader::class)->makePartial();
        $loader->shouldReceive('addPath')
            ->once()
            ->with($templateDirectory, 'my_package');

        $factory->create($loader);

        $this->assertTrue(true);
    }

    public function testReservedThemeNamespaceFallsBackToSiteThemeAndCoexistsWithCustomNamespaces(): void
    {
        $customDirectory = '/my/custom/templates';
        $siteThemeDirectory = '/my/site/theme';

        $currentPage = Mockery::mock();
        $currentPage->shouldReceive('getCollectionThemeObject')->andReturn(null);

        $siteTheme = Mockery::mock();
        $siteTheme->shouldReceive('getThemeDirectory')->andReturn($siteThemeDirectory);

        Mockery::mock('alias:Concrete\Core\Page\Page')
            ->shouldReceive('getCurrentPage')
            ->andReturn($currentPage);
        Mockery::mock('alias:Concrete\Core\Page\Theme\Theme')
            ->shouldReceive('getSiteTheme')
            ->andReturn($siteTheme);

        $factory = $this->createFactory();
        $factory->addNamespace($customDirectory, 'my_package');

        $loader = Mockery::mock(FilesystemLoader::class)->makePartial();
        $loader->shouldReceive('addPath')
            ->once()
            ->with($customDirectory, 'my_package');
        $loader->shouldReceive('addPath')
            ->once()
            ->with($siteThemeDirectory, 'theme');

        $factory->create($loader);
    }

    private function createFactory(): TwigFactory
    {
        return new TwigFactory(Mockery::mock(CacheInterface::class), false);
    }
}
