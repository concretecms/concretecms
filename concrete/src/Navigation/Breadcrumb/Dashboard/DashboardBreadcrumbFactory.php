<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;

class DashboardBreadcrumbFactory
{

    /**
     * @var Navigation
     */
    protected $navigation;

    public function __construct(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function getBreadcrumb(Page $dashboardPage): BreadcrumbInterface
    {
        $pages = array_reverse($this->navigation->getTrailToCollection($dashboardPage));
        $breadcrumb = new DashboardBreadcrumb();
        foreach($pages as $page) {
            $breadcrumb->add(new Item($page->getCollectionLink(), t($page->getCollectionName())));
        }
        $breadcrumb->add(new Item($dashboardPage->getCollectionLink(), t($dashboardPage->getCollectionName())));
        return $breadcrumb;
    }
}
