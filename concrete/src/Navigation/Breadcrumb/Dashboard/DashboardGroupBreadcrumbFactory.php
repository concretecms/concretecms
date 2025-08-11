<?php

namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\Tree\Type\Group;

class DashboardGroupBreadcrumbFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var DashboardBreadcrumbFactory
     */
    protected $breadcrumbFactory;

    /**
     * @var Navigation
     */
    protected $navigation;

    public function __construct(DashboardBreadcrumbFactory $breadcrumbFactory, Navigation $navigation)
    {
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->navigation = $navigation;
    }

    public function getBreadcrumb(Page $dashboardPage, $mixed = null): BreadcrumbInterface
    {
        $pages = array_reverse($this->navigation->getTrailToCollection($dashboardPage));
        $breadcrumb = new DashboardBreadcrumb();
        foreach($pages as $page) {
            $breadcrumb->add(new Item(Url::to($page->getCollectionLink()), $page->getCollectionName()));
        }
        $breadcrumb->add(new Item(Url::to($dashboardPage->getCollectionLink()), $dashboardPage->getCollectionName()));

        if ($mixed instanceof GroupFolder || $mixed instanceof \Concrete\Core\Tree\Node\Type\Group) {
            if ($mixed->getTreeNodeParentID() > 0) {
                $nodes = array_reverse($mixed->getTreeNodeParentArray());

                // Shift the group manager node off the top.

                array_shift($nodes);

                /**
                 * @var $nodes GroupFolder[]
                 */
                foreach($nodes as $node) {
                    $item = new Item(
                        $this->app->make('url')->to(
                            '/dashboard/users/groups', 'folder', $node->getTreeNodeID()
                        ),
                        $node->getTreeNodeDisplayName()
                    );
                    $breadcrumb->add($item);
                }

                $item = new Item(
                    $this->app->make('url')->to(
                        '/dashboard/users/groups', 'folder', $mixed->getTreeNodeID()
                    ),
                    $mixed->getTreeNodeDisplayName()
                );
                $breadcrumb->add($item);
            }
        } else if ($mixed instanceof SavedGroupSearch) {
            $item = new Item(
                $this->app->make('url')->to(
                    '/dashboard/users/groups', 'preset', $mixed->getID()
                ),
                $mixed->getPresetName()
            );
            $breadcrumb->add($item);
        }
        return $breadcrumb;
    }
}
