<?php

namespace Concrete\Tests\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Sitemap\PageListGenerator;
use Concrete\Tests\TestCase;
use Mockery;

/**
 * Covers PageListGenerator::getSite() — the lazy-init that was fixed to prefer
 * getActiveSiteForEditing() over getDefault() in multisite installs.
 */
class PageListGeneratorTest extends TestCase
{
    // -------------------------------------------------------------------------
    // getSite() lazy-init priority
    // -------------------------------------------------------------------------

    public function testGetSiteReturnsActiveSiteWhenAvailable(): void
    {
        $activeSite = Mockery::mock(Site::class);

        $siteService = new \stdClass();
        $siteService = Mockery::mock();
        $siteService->shouldReceive('getActiveSiteForEditing')->once()->andReturn($activeSite);
        $siteService->shouldNotReceive('getDefault');

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('make')->with('site')->andReturn($siteService);

        $generator = new PageListGenerator($app);
        $result = $generator->getSite();

        $this->assertSame($activeSite, $result);
    }

    public function testGetSiteFallsBackToDefaultWhenNoActiveSite(): void
    {
        $defaultSite = Mockery::mock(Site::class);

        $siteService = Mockery::mock();
        $siteService->shouldReceive('getActiveSiteForEditing')->once()->andReturn(null);
        $siteService->shouldReceive('getDefault')->once()->andReturn($defaultSite);

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('make')->with('site')->andReturn($siteService);

        $generator = new PageListGenerator($app);
        $result = $generator->getSite();

        $this->assertSame($defaultSite, $result);
    }

    public function testGetSiteIsCachedAfterFirstCall(): void
    {
        $activeSite = Mockery::mock(Site::class);

        $siteService = Mockery::mock();
        // Service must only be consulted once even when getSite() is called twice.
        $siteService->shouldReceive('getActiveSiteForEditing')->once()->andReturn($activeSite);

        $app = Mockery::mock(Application::class);
        $app->shouldReceive('make')->with('site')->once()->andReturn($siteService);

        $generator = new PageListGenerator($app);
        $first  = $generator->getSite();
        $second = $generator->getSite();

        $this->assertSame($first, $second);
    }

    public function testSetSiteOverridesLazyInit(): void
    {
        $explicitSite = Mockery::mock(Site::class);

        $app = Mockery::mock(Application::class);
        // make('site') must never be called when a site has been set explicitly.
        $app->shouldNotReceive('make');

        $generator = new PageListGenerator($app);
        $generator->setSite($explicitSite);

        $this->assertSame($explicitSite, $generator->getSite());
    }

    public function testSetSiteResetsMultilingualCache(): void
    {
        $siteA = Mockery::mock(Site::class);
        $siteB = Mockery::mock(Site::class);

        $app = Mockery::mock(Application::class);
        $app->shouldNotReceive('make');

        $generator = new PageListGenerator($app);
        $generator->setSite($siteA);
        $generator->setSite($siteB);

        $this->assertSame($siteB, $generator->getSite());
    }
}
