<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;

class FavoritesNavigationFactory
{

    public $items = [
        '/dashboard/welcome',
        '/dashboard/composer/write',
        '/dashboard/composer/drafts',
        '/dashboard/sitemap/full',
        '/dashboard/sitemap/search',
        '/dashboard/files/search',
        '/dashboard/files/sets',
        '/dashboard/reports/forms',
    ];

    /**
     * @var FavoritesNavigationCache
     */
    protected $cache;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var JsonSerializer
     */
    protected $serializer;


    public function __construct(FavoritesNavigationCache $cache, User $user, JsonSerializer $serializer)
    {
        $this->cache = $cache;
        $this->user = $user;
        $this->serializer = $serializer;
    }

    public function createNavigation(Page $startingPage = null): Navigation
    {
        if (!$this->cache->has()) {
            $navigation = null;
            $favorites = $this->user->config('DASHBOARD_FAVORITES');
            if ($favorites) {
                $navigation = $this->serializer->deserialize($favorites, Navigation::class, 'json');
            }
            if (!$navigation instanceof Navigation) {
                $navigation = new Navigation();
                foreach ($this->items as $path) {
                    $page = Page::getByPath($path);
                    if ($page && !$page->isError()) {
                        $checker = new Checker($page);
                        if ($checker->canViewPage()) {
                            $navigation->add(new PageItem($page));
                        }
                    }
                }
            }
            $this->cache->set($navigation);
        } else {
            $navigation = $this->cache->get();
        }
        return $navigation;
    }

}
