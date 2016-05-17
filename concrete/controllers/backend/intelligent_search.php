<?php
namespace Concrete\Controller\Backend;

class IntelligentSearch extends UserInterface
{
    protected function canAccess()
    {
        $sh = \Core::make('helper/concrete/dashboard/sitemap');
        if (!$sh->canRead()) {
            return false;
        }

        return true;
    }

    public function view()
    {
        session_write_close();

        $keywords = $_REQUEST['q'];
        $pl = new \PageList();
        $pl->filterByName($keywords);
        $pl->sortBy('cID', 'asc');
        $pl->setItemsPerPage(5);
        $pl->setPermissionsChecker(function ($page) {
            $pp = new \Permissions($page);

            return $pp->canViewPageInSitemap();
        });
        $pagination = $pl->getPagination();
        $pages = $pagination->getCurrentPageResults();

        $results = array();
        $nh = \Core::make('helper/navigation');
        foreach ($pages as $c) {
            $obj = new \stdClass();
            $obj->href = $nh->getLinkToCollection($c);
            $obj->cID = $c->getCollectionID();
            $obj->name = $c->getCollectionName();
            $results[] = $obj;
        }
        echo json_encode($results);
        \Core::shutdown(array('jobs' => true));
    }
}
