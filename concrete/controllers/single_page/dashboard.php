<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

class Dashboard extends DashboardPageController
{
    public $helpers = array('form');

    public function view()
    {
        $this->enableNativeMobile();
        $categories = array();
        $c = Page::getCurrentPage();
        $children = $c->getCollectionChildrenArray(true);
        foreach ($children as $cID) {
            $nc = Page::getByID($cID, 'ACTIVE');
            $ncp = new Permissions($nc);
            if ($ncp->canRead() && (!$nc->getAttribute('exclude_nav'))) {
                $categories[] = $nc;
            }
        }
        $this->set('categories', $categories);
    }
}
