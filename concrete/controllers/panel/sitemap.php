<?php
namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Loader;
use PageType;
use Page as ConcretePage;
use Permissions;

class Sitemap extends BackendInterfaceController
{
    protected $viewPath = '/panels/sitemap';
    protected $frequentPageTypes = array();
    protected $otherPageTypes = array();
    protected $site;

    public function on_start()
    {
        $sh = Loader::helper('concrete/dashboard/sitemap');
        $this->canViewSitemap = $sh->canRead();
        $this->site = \Core::make('site')->getSite();
        $type = $this->site->getType();
        $frequentlyUsed = PageType::getFrequentlyUsedList($type);
        foreach ($frequentlyUsed as $pt) {
            $ptp = new Permissions($pt);
            if ($ptp->canAddPageType()) {
                $this->frequentPageTypes[] = $pt;
            }
        }

        $otherPageTypes = PageType::getInfrequentlyUsedList($type);
        foreach ($otherPageTypes as $pt) {
            $ptp = new Permissions($pt);
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
        $this->requireAsset('core/sitemap');

        $drafts = ConcretePage::getDrafts();
        $mydrafts = array();
        foreach ($drafts as $d) {
            $dp = new Permissions($d);
            if ($dp->canEditPageContents()) {
                $mydrafts[] = $d;
            }
        }

        $this->set('frequentPageTypes', $this->frequentPageTypes);
        $this->set('otherPageTypes', $this->otherPageTypes);
        $this->set('drafts', $mydrafts);
        $this->set('canViewSitemap', $this->canViewSitemap);
        $this->set('site', $this->site);
    }
}
