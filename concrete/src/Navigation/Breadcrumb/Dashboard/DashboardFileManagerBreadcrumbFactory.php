<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Type\FileFolder;

class DashboardFileManagerBreadcrumbFactory implements ApplicationAwareInterface
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

        if ($mixed instanceof FileFolder) {
            if ($mixed->getTreeNodeParentID() > 0) {
                $nodes = array_reverse($mixed->getTreeNodeParentArray());

                // Shift the file manager node off the top.

                array_shift($nodes);

                /**
                 * @var $nodes FileFolder[]
                 */
                foreach($nodes as $node) {
                    $item = new Item(
                        $this->app->make('url')->to(
                            '/dashboard/files/search', 'folder', $node->getTreeNodeID()
                        ),
                        $node->getTreeNodeDisplayName()
                    );
                    $breadcrumb->add($item);
                }

                $item = new Item(
                    $this->app->make('url')->to(
                        '/dashboard/files/search', 'folder', $mixed->getTreeNodeID()
                    ),
                    $mixed->getTreeNodeDisplayName()
                );
                $breadcrumb->add($item);
            }
        } else if ($mixed instanceof SavedFileSearch) {
            $item = new Item(
                $this->app->make('url')->to(
                    '/dashboard/files/search', 'preset', $mixed->getID()
                ),
                $mixed->getPresetName()
            );
            $breadcrumb->add($item);
        }
        return $breadcrumb;
    }
}
