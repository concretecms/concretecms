<?php
namespace Concrete\Controller\Backend\Page;

use Concrete\Controller\Backend\UserInterface;
use Core;
use Symfony\Component\HttpFoundation\JsonResponse;
use Page;

class SitemapSelector extends UserInterface
{

    public function getViewObject()
    {
        return null;
    }

    public function canAccess()
    {
        return Core::make('token')->validate('select_sitemap');
    }

    protected function addParentsTo($expanded, $page)
    {
        $nav = Core::make('helper/navigation');
        $pages = $nav->getTrailToCollection($page);
        foreach($pages as $c) {
            if (!in_array($c->getCollectionID(), $expanded)) {
                $expanded[] = $c->getCollectionID();
            }
        }
        return $expanded;
    }

    public function view()
    {
        $dh = Core::make('helper/concrete/dashboard/sitemap');

        // set expanded nodes based on all the selected nodes.
        $expanded = array(1);
        if (is_array($this->request->query->get('selected'))) {
            foreach($this->request->query->get('selected') as $cID) {
                $page = Page::getByID(intval($cID));
                $expanded = $this->addParentsTo($expanded, $page);
            }
        } else if ($this->request->query->has('selected') && $this->request->query->get('selected') > 0) {
            $page = Page::getByID(intval($this->request->query->get('selected')));
            $expanded = $this->addParentsTo($expanded, $page);
        }
        $dh->setExpandedNodes($expanded);
        $nodes = $dh->getSubNodes($this->request->query->get('cParentID'));
        return new JsonResponse($nodes);
    }

}

