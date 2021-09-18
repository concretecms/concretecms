<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;

class Sitemap extends BackendInterfaceController
{
    protected $viewPath = '/panels/sitemap';
    protected $frequentPageTypes = array();
    protected $otherPageTypes = array();
    protected $site;
    protected $canViewSitemap;

    public function on_start()
    {
        $sh = $this->app->make('helper/concrete/dashboard/sitemap');
        $this->canViewSitemap = $sh->canRead();
        $this->site = $this->app->make('site')->getSite();
        $type = $this->site->getType();
        $frequentlyUsed = Type::getFrequentlyUsedList($type);
        foreach ($frequentlyUsed as $pt) {
            $ptp = new Checker($pt);
            if ($ptp->canAddPageType()) {
                $this->frequentPageTypes[] = $pt;
            }
        }

        $otherPageTypes = Type::getInfrequentlyUsedList($type);
        foreach ($otherPageTypes as $pt) {
            $ptp = new Checker($pt);
            if ($ptp->canAddPageType()) {
                $this->otherPageTypes[] = $pt;
            }
        }
    }

    protected function canAccess()
    {
        return $this->canViewSitemap || count($this->frequentPageTypes) > 0 || count($this->otherPageTypes) > 0;
    }

    public function view()
    {
        $drafts = Page::getDrafts($this->site);
        $mydrafts = array();
        foreach ($drafts as $d) {
            $dp = new Checker($d);
            $pt = $d->getPagetypeObject();
            $tp = new Checker($pt);
            if ($tp->canEditPageTypeDrafts() || $dp->canEditPageContents()) {
                $mydrafts[] = $d;
            }
        }

        $siteTreeID = 0;
        if ($this->request->query->has('cID')) {
            $page = Page::getByID(intval($this->request->query->get('cID')));
            if ($page && !$page->isError()) {
                $siteTreeID = $page->getSiteTreeID();
            }
        }

        $this->set('frequentPageTypes', $this->frequentPageTypes);
        $this->set('otherPageTypes', $this->otherPageTypes);
        $this->set('drafts', $mydrafts);
        $this->set('canViewSitemap', $this->canViewSitemap);
        $this->set('siteTreeID', $siteTreeID);
    }
}
