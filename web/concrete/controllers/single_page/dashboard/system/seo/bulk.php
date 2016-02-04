<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Multilingual\Page\Section\Section;

class Bulk extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui');

    /**
     * Get the localized site name.
     *
     * @param string $locale
     *
     * @return string
     */
    protected function getSiteNameForLocale($locale)
    {
        static $names = array();
        if (!isset($names[$locale])) {
            $prevLocale = \Localization::activeLocale();
            if ($prevLocale !== $locale) {
                \Localization::changeLocale($locale);
            }
            $names[$locale] = tc('SiteName', $this->app->make('config')->get('concrete.site'));
            if ($prevLocale !== $locale) {
                \Localization::changeLocale($prevLocale);
            }
        }

        return $names[$locale];
    }

    /**
     * Get the site name localized for a specific page.
     *
     * @param \Concrete\Core\Page\Page $page
     *
     * @return string
     */
    public function getSiteNameForPage(\Concrete\Core\Page\Page $page)
    {
        static $multilingual;
        static $defaultLocale;

        if (!isset($multilingual)) {
            $multilingual = $this->app->make('multilingual/detector')->isEnabled();
        }
        if ($multilingual) {
            if (!isset($defaultLocale)) {
                $defaultLocale = $this->app->make('config')->get('concrete.locale') ?: 'en_US';
            }
            $section = Section::getBySectionOfSite($page);
            if ($section) {
                $locale = $section->getLocale();
            } else {
                $locale = $defaultLocale;
            }
            $siteName = $this->getSiteNameForLocale($locale);
        } else {
            $siteName = $this->app->make('config')->get('concrete.site');
        }

        return $siteName;
    }

    public function view()
    {
        $html = Loader::helper('html');
        $this->requireAsset('javascript', 'jquery/textcounter');
        $pageList = $this->getRequestedSearchResults();
        if (is_object($pageList)) {
            $pagination = $pageList->getPagination();
            $pages = $pagination->getCurrentPageResults();
            $this->set('pageList', $pageList);
            $this->set('pages', $pages);
            $paginationView = false;
            if ($pagination->haveToPaginate()) {
                $paginationView = $pagination->renderView('dashboard');
            }
            $this->set('pagination', $paginationView);
        }
    }

    public function saveRecord()
    {
        $text = Loader::helper('text');
        $success = t('success');
        $cID = $this->post('cID');
        $c = Page::getByID($cID);
        $siteName = $this->getSiteNameForPage($c);
        if (trim(sprintf(Config::get('concrete.seo.title_format'), $siteName, $c->getCollectionName())) != trim($this->post('meta_title')) && $this->post('meta_title')) {
            $c->setAttribute('meta_title', trim($this->post('meta_title')));
        }

        if (trim(htmlspecialchars($c->getCollectionDescription(), ENT_COMPAT, APP_CHARSET)) != trim($this->post('meta_description')) && $this->post('meta_description')) {
            $c->setAttribute('meta_description', trim($this->post('meta_description')));
        }
        $cHandle = $this->post('collection_handle');
        $c->update(array('cHandle' => $cHandle));
        $c->rescanCollectionPath();
        $newPath = Page::getCollectionPathFromID($cID);
        $newHandle = $text->urlify($cHandle);
        $result = array('success' => $success, 'cID' => $cID, 'cHandle' => $newHandle, 'newPath' => $newHandle, 'newLink' => $newPath);
        echo Loader::helper('json')->encode($result);
        exit;
    }

    /**
     * @return bool|PageList
     */
    public function getRequestedSearchResults()
    {
        $dh = Loader::helper('concrete/dashboard/sitemap');
        if (!$dh->canRead()) {
            return false;
        }

        $pageList = new PageList();

        if ($_REQUEST['submit_search']) {
            $pageList->resetSearchRequest();
        }

        $req = $_REQUEST;
        $pageList->displayUnapprovedPages();

        $pageList->sortBy('cDateModified', 'desc');

        $cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);

        if ($cvName != '') {
            $pageList->filterByName($cvName);
        }

        if ($req['cParentIDSearchField'] > 0) {
            if ($req['cParentAll'] == 1) {
                $pc = Page::getByID($req['cParentIDSearchField']);
                $cPath = $pc->getCollectionPath();
                $pageList->filterByPath($cPath);
            } else {
                $pageList->filterByParentID($req['cParentIDSearchField']);
            }
            $parentDialogOpen = 1;
        }

        $keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
        $pageList->filterByKeywords($keywords, true);

        if ($req['numResults']) {
            $pageList->setItemsPerPage($req['numResults']);
        }

        if ($req['ptID']) {
            $pageList->filterByPageTypeID($req['ptID']);
        }

        if ($_REQUEST['noDescription'] == 1) {
            $pageList->filter('CollectionSearchIndexAttributes.ak_meta_description', null, '=');
            $this->set('descCheck', true);
            $parentDialogOpen = 1;
        }

        $this->set('searchRequest', $req);
        $this->set('parentDialogOpen', $parentDialogOpen);

        return $pageList;
    }
}
