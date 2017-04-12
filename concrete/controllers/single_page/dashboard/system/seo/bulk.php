<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Multilingual\Page\Section\Section;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Localization\Localization;

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
            $names[$locale] = tc('SiteName', $this->app->make('site')->getSite()->getSiteName());
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
                $defaultLocale = $this->app->make('config')->get('concrete.locale') ?: Localization::BASE_LOCALE;
            }
            $section = Section::getBySectionOfSite($page);
            if ($section) {
                $locale = $section->getLocale();
            } else {
                $locale = $defaultLocale;
            }
            $siteName = $this->getSiteNameForLocale($locale);
        } else {
            $siteName = $this->app->make('site')->getSite()->getSiteName();
        }

        return $siteName;
    }

    public function view()
    {
        $html = $this->app->make('helper/html');
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
        $cID = $this->post('cID');

        if (!$this->token->validate('save_seo_record_' . $cID)) {
            $error = t('Invalid CSRF token. Please refresh and try again.');
            return JsonResponse::create(array('message' => $error));
        }

        $text = $this->app->make('helper/text');
        $success = t('success');
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new \RuntimeException(t('Unable to find the specified page'));
        }
        $titleFormat = $this->app->make('config')->get('concrete.seo.title_format');
        $siteName = $this->getSiteNameForPage($c);
        if (trim(sprintf($titleFormat, $siteName, $c->getCollectionName())) != trim($this->post('meta_title')) && $this->post('meta_title')) {
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

        JsonResponse::create($result)->send();
        exit;
    }

    /**
     * @return bool|PageList
     */
    public function getRequestedSearchResults()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        if (!$dh->canRead()) {
            return false;
        }

        $pageList = new PageList();

        if ($this->request('submit_search')) {
            $pageList->resetSearchRequest();
        }

        $pageList->displayUnapprovedPages();

        $pageList->sortBy('cDateModified', 'desc');

        $cvName = $this->request('cvName');
        if ($cvName) {
            $pageList->filterByName($cvName);
        }

        $cParentIDSearchField = $this->request('cParentIDSearchField');
        if ($cParentIDSearchField > 0) {
            if ($this->request('cParentAll') == 1) {
                $pc = Page::getByID($cParentIDSearchField);
                if ($pc && !$pc->isError()) {
                    $cPath = $pc->getCollectionPath();
                    $pageList->filterByPath($cPath);
                }
            } else {
                $pageList->filterByParentID($cParentIDSearchField);
            }
            $parentDialogOpen = 1;
        }

        $keywords = $this->request('keywords');
        $pageList->filterByKeywords($keywords, true);

        $numResults = $this->request('numResults');
        if ($numResults) {
            $pageList->setItemsPerPage($numResults);
        }

        $ptID = $this->request('ptID');
        if ($ptID) {
            $pageList->filterByPageTypeID($ptID);
        }

        if ($this->request('noDescription') == 1) {
            $pageList->filter(false, "csi.ak_meta_description is null or csi.ak_meta_description = ''");
            $this->set('descCheck', true);
            $parentDialogOpen = 1;
        } else {
            $parentDialogOpen = null;
        }

        $this->set('searchRequest', $_REQUEST);
        $this->set('parentDialogOpen', $parentDialogOpen);

        return $pageList;
    }
}
