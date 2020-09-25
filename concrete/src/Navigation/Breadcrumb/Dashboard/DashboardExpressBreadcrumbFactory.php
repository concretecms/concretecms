<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Concrete\Core\Tree\Node\Type\FileFolder;

class DashboardExpressBreadcrumbFactory implements ApplicationAwareInterface
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
        $url = $this->app->make('url');
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);

        if ($mixed instanceof Node) {
            $parentNode = $mixed;
        } else if ($mixed instanceof SavedExpressSearch || $mixed instanceof Entry) {
            $parentNode = $mixed->getEntity()->getEntityResultsNodeObject();
        }
        if (isset($parentNode)) {
            $nodes = array_reverse($parentNode->getTreeNodeParentArray());
            array_shift($nodes);
            array_shift($nodes);

            /**
             * @var $nodes FileFolder[]
             */
            foreach($nodes as $node) {
                $itemUrl = $url->to(
                    $dashboardPage->getCollectionPath(), $node->getTreeNodeID()
                );
                $item = new Item($itemUrl, $node->getTreeNodeDisplayName());
                $breadcrumb->add($item);
            }

            if ($parentNode instanceof ExpressEntryResults) {
                $itemUrl = $url->to(
                    $dashboardPage->getCollectionPath(), 'results', $parentNode->getEntity()->getId()
                );
            } else {
                $itemUrl = $url->to(
                    $dashboardPage->getCollectionPath(), $parentNode->getTreeNodeID()
                );
            }
            $item = new Item($itemUrl, $parentNode->getTreeNodeDisplayName());
            $breadcrumb->add($item);
        }

        if ($mixed instanceof SavedExpressSearch) {
            $item = new Item(
                $this->app->make('url')->to(
                    $dashboardPage->getCollectionPath(), 'preset', $mixed->getID()
                ),
                $mixed->getPresetName()
            );
            $breadcrumb->add($item);
        } else if ($mixed instanceof Entry) {
            $item = new Item(
                $this->app->make('url')->to(
                    $dashboardPage->getCollectionPath(), 'view_entry', $mixed->getID()
                ),
                $mixed->getLabel()
            );
            $breadcrumb->add($item);
        }
        return $breadcrumb;
    }
}
