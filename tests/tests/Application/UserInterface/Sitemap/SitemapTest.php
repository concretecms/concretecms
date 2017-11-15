<?php

namespace Concrete\Tests\Application\UserInterface\Sitemap;

use Core;
use PHPUnit_Framework_TestCase;

class SitemapTest extends PHPUnit_Framework_TestCase
{
    public function testGetBasicSitemapTreeCollection()
    {
        $locale = $this->createLocale('en', 'US', 1, 'English');
        $site = $this->createSite([$locale]);
        $service = $this->createService([$site]);

        $provider = Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider', ['siteService' => $service]);
        $provider->ignorePermissions();
        $this->assertInstanceOf('Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface', $provider);

        $collection = $provider->getTreeCollection();
        $this->assertInstanceOf('Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface', $collection);

        $entries = $collection->getEntries();
        $entryGroups = $collection->getEntryGroups();

        $this->assertCount(0, $entryGroups);
        $this->assertCount(1, $entries);
        $this->assertFalse($collection->displayMenu());

        $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter($collection);
        $json = $formatter->jsonSerialize();

        $this->assertEquals(0, $json['displayMenu']);
    }

    public function testGetMultilingualSitemapTreeCollection()
    {
        $locale1 = $this->createLocale('en', 'US', 1, 'English');
        $locale2 = $this->createLocale('de', 'DE', 2, 'German');
        $locale3 = $this->createLocale('fr', 'FR', 3, 'French');
        $site = $this->createSite([$locale1, $locale2, $locale3]);
        $service = $this->createService([$site]);

        $provider = Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider', ['siteService' => $service]);
        $provider->ignorePermissions();
        $collection = $provider->getTreeCollection();

        $entries = $collection->getEntries();
        $entryGroups = $collection->getEntryGroups();

        $this->assertCount(3, $entries);
        $this->assertCount(0, $entryGroups);
        $this->assertTrue($collection->displayMenu());

        $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter($collection);
        $json = $formatter->jsonSerialize();

        $this->assertEquals(1, $json['displayMenu']);
        $this->assertEquals(2, $json['entries'][1]['siteTreeID']);
        $this->assertEquals('<div class="ccm-sitemap-tree-selector-option"><img id="ccm-region-flag-us" class="ccm-region-flag" src="/path/to/server/concrete/images/countries/us.png" alt="us"><span class="ccm-sitemap-tree-menu-label">English</span></div>', $json['entries'][0]['element']);
        $this->assertEquals('<div class="ccm-sitemap-tree-selector-option"><img id="ccm-region-flag-de" class="ccm-region-flag" src="/path/to/server/concrete/images/countries/de.png" alt="de"><span class="ccm-sitemap-tree-menu-label">German</span></div>', $json['entries'][1]['element']);
        $this->assertEmpty($json['entryGroups']);
    }

    public function testMultipleSiteNoMultilingualCollection()
    {
        $locale1 = $this->createLocale('en', 'US', 1, 'English');
        $locale2 = $this->createLocale('en', 'US', 2, 'English');
        $site1 = $this->createSite([$locale1]);
        $site2 = $this->createSite([$locale2]);
        $site1->expects($this->once())->method('getSiteTreeID')->willReturn(1);
        $site2->expects($this->once())->method('getSiteTreeID')->willReturn(2);
        $site1->expects($this->any())->method('getSiteName')->willReturn('Site A');
        $site2->expects($this->any())->method('getSiteName')->willReturn('Site B');

        $service = $this->createService([$site1, $site2]);

        $provider = Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider', ['siteService' => $service]);
        $provider->ignorePermissions();
        $collection = $provider->getTreeCollection();

        $entries = $collection->getEntries();
        $entryGroups = $collection->getEntryGroups();

        $this->assertCount(2, $entries);
        $this->assertCount(0, $entryGroups);
        $this->assertTrue($collection->displayMenu());

        $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter($collection);
        $json = $formatter->jsonSerialize();

        $this->assertEquals(1, $json['displayMenu']);
        $this->assertEquals(2, $json['entries'][1]['siteTreeID']);
        $this->assertEquals('<div class="ccm-sitemap-tree-selector-option"><span class="ccm-sitemap-tree-menu-label">Site A</span></div>', $json['entries'][0]['element']);
    }

    public function testMultipleSiteMultilingualCollection()
    {
        $locale1 = $this->createLocale('en', 'US', 1, 'English');
        $locale2 = $this->createLocale('de', 'DE', 2, 'German');
        $locale3 = $this->createLocale('fr', 'FR', 3, 'French');
        $site1 = $this->createSite([$locale1, $locale2, $locale3]);
        $site1->expects($this->once())->method('getSiteName')->willReturn('Site A');
        $site1->expects($this->once())->method('getSiteID')->willReturn(1);

        $locale4 = $this->createLocale('en', 'US', 4, 'English');
        $site2 = $this->createSite([$locale4]);
        $site2->expects($this->once())->method('getSiteName')->willReturn('Site B');
        $site2->expects($this->once())->method('getSiteID')->willReturn(2);

        $locale5 = $this->createLocale('de', 'DE', 5, 'German');
        $locale6 = $this->createLocale('ja', 'JP', 6, 'Japanese');
        $site3 = $this->createSite([$locale5, $locale6]);
        $site3->expects($this->once())->method('getSiteName')->willReturn('Site C');
        $site3->expects($this->once())->method('getSiteID')->willReturn(3);

        $locale7 = $this->createLocale('en', 'GB', 7, 'British English');
        $site4 = $this->createSite([$locale7]);
        $site4->expects($this->once())->method('getSiteName')->willReturn('Site D');
        $site4->expects($this->once())->method('getSiteID')->willReturn(4);

        $service = $this->createService([$site1, $site2, $site3, $site4]);

        $provider = Core::make('Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider', ['siteService' => $service]);
        $provider->ignorePermissions();

        $collection = $provider->getTreeCollection();

        $entries = $collection->getEntries();
        $entryGroups = $collection->getEntryGroups();

        $this->assertCount(7, $entries);
        $this->assertCount(4, $entryGroups);
        $this->assertTrue($collection->displayMenu());

        $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter($collection);
        $json = $formatter->jsonSerialize();

        $this->assertEquals('Site A', $json['entryGroups'][0]['label']);
        $this->assertEquals(1, $json['entryGroups'][0]['value']);
    }

    protected function createSite($localesToReturn)
    {
        $site = $this->getMockBuilder('\Concrete\Core\Entity\Site\Site')
            ->disableOriginalConstructor()
            ->getMock();

        $site->expects($this->any())
            ->method('getLocales')
            ->will($this->returnValue($localesToReturn));

        return $site;
    }

    protected function createService($sitesToReturn)
    {
        $service = $this->getMockBuilder('\Concrete\Core\Site\Service')
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects($this->any())
            ->method('getList')
            ->will($this->returnValue($sitesToReturn));

        return $service;
    }

    protected function createLocale($language, $country, $siteTreeIDToReturn, $languageText)
    {
        $locale = $this->getMockBuilder('\Concrete\Core\Entity\Site\Locale')
            ->disableOriginalConstructor()
            ->getMock();

        $locale->expects($this->any())->method('getLanguage')->willReturn($language);
        $locale->expects($this->any())->method('getCountry')->willReturn($country);
        $locale->expects($this->any())->method('getLanguageText')->willReturn($languageText);

        $tree = $this->getMockBuilder('\Concrete\Core\Entity\Site\SiteTree')
            ->disableOriginalConstructor()
            ->getMock();

        $tree->expects($this->any())->method('getSiteTreeID')->willReturn($siteTreeIDToReturn);

        $locale->expects($this->any())->method('getSiteTree')->willReturn($tree);

        return $locale;
    }
}
