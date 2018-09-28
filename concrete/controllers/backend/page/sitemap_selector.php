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
        foreach ($pages as $c) {
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
            foreach ($this->request->query->get('selected') as $cID) {
                $page = Page::getByID(intval($cID));
                $expanded = $this->addParentsTo($expanded, $page);
            }
        } elseif ($this->request->query->has('selected') && $this->request->query->get('selected') > 0) {
            $page = Page::getByID(intval($this->request->query->get('selected')));
            $expanded = $this->addParentsTo($expanded, $page);
        }
        $dh->setExpandedNodes($expanded);
        $request = $this->request->query->all();
        $callback = function ($node) use ($request) {
            if (isset($request['filters']) && is_array($request['filters'])) {
                foreach ($request['filters'] as $key => $filter) {
                    if ($key == 'ptID' && $filter != $node->ptID) {
                        $node->hideCheckbox = true;
                    }
                }
            }

            return $node;
        };
        if ($this->request->query->get('cParentID') > 0) {
            // this is an open node request
            $response = $dh->getSubNodes($this->request->query->get('cParentID'), $callback);
        } else if ($this->request->query->get('startingPoint') && $this->request->query->get('startingPoint') > 1) {
            $startingPoint = Page::getByID($this->request->query->get('startingPoint'));
            if ($startingPoint && !$startingPoint->isError()) {
                $response = [
                    'children' => [
                        $dh->getNode($startingPoint)
                    ]
                ];
            }
        } else {
            $service = \Core::make('site');
            if (isset($_REQUEST['siteTreeID']) && $_REQUEST['siteTreeID'] > 0) {
                $tree = $service->getSiteTreeByID($_REQUEST['siteTreeID']);
            } else {
                $tree = $service->getActiveSiteForEditing()->getSiteTreeObject();
            }

            $provider = \Core::make('\Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider');
            $collection = $provider->getTreeCollection($tree);
            $formatter = new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionJsonFormatter($collection);

            $response = [
                'children' => $dh->getSubNodes($tree, $callback),
                'trees' => $formatter
            ];

        }

        return new JsonResponse($response);
    }
}
