<?php

namespace Concrete\Tests\Navigation;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\AbstractNavigationCache;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Navigation;
use Concrete\Core\Localization\Localization;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DashboardNavigationCacheTest extends TestCase
{
    public function testNavigationCacheIsSeparatedByUser(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $cache = $this->createCache($session);

        $session->set('uID', 1);
        $cache->set(new Navigation());

        $session->set('uID', 2);
        $this->assertFalse($cache->has());

        $cache->set(new Navigation());

        $this->assertTrue($session->has('dashboard_favorites_menu@en_US@1'));
        $this->assertTrue($session->has('dashboard_favorites_menu@en_US@2'));
    }

    public function testClearRemovesNavigationCacheForEveryUser(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $cache = $this->createCache($session);

        $session->set('uID', 1);
        $cache->set(new Navigation());

        $session->set('uID', 2);
        $cache->set(new Navigation());

        $cache->clear();

        $this->assertFalse($session->has('dashboard_favorites_menu@en_US@1'));
        $this->assertFalse($session->has('dashboard_favorites_menu@en_US@2'));
    }

    private function createCache(Session $session): AbstractNavigationCache
    {
        $localization = $this->createMock(Localization::class);
        $localization->method('getLocale')->willReturn(Localization::BASE_LOCALE);

        return new class ($session, $localization) extends AbstractNavigationCache {
            public function getIdentifier(): string
            {
                return 'dashboard_favorites_menu';
            }
        };
    }
}
