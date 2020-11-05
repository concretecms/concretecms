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
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\UserInfo;

class DashboardUserBreadcrumbFactory implements ApplicationAwareInterface
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
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);

        if ($mixed instanceof UserInfo) {
            $item = new Item(
                $this->app->make('url')->to(
                    '/dashboard/users/search', 'edit', $mixed->getUserID()
                ),
                $mixed->getUserDisplayName()
            );

            $breadcrumb->add($item);
        }

        return $breadcrumb;
    }
}
