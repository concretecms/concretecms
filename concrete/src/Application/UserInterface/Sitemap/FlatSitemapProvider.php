<?php

namespace Concrete\Core\Application\UserInterface\Sitemap;

class FlatSitemapProvider extends StandardSitemapProvider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\ProviderInterface::getRequestedNodes()
     */
    public function getRequestedNodes()
    {
        $dh = $this->getSitemapDataProvider();
        $cParentID = 0;
        $parent = null;
        if ($this->request->query->has('cParentID')) {
            $cParentID = $this->request->query->get('cParentID');
        }
        if ($cParentID > 0) {
            $c = \Page::getByID($cParentID);
            $parent = \Page::getByID($c->getCollectionParentID());
        }

        if (is_object($parent) && !$parent->isError()) {
            $n = $dh->getNode($parent->getCollectionID());
            $n->icon = 'fa fa-angle-double-up';
            $n->expanded = true;
            $n->displaySingleLevel = true;

            $p = $dh->getNode($this->request->query->get('cParentID'));
            $p->expanded = true;
            $p->children = $dh->getSubNodes($this->request->query->get('cParentID'));
            $n->children = [$p];
            $nodes[] = $n;
        } else {
            if ($cParentID > 0) {
                $n = $dh->getNode($cParentID);
                $n->children = $dh->getSubNodes($this->request->query->get('cParentID'));
                $nodes[] = $n;
            } else {
                $nodes = $dh->getSubNodes($this->getRequestedSiteTree());
            }
        }

        return $nodes;
    }
}
