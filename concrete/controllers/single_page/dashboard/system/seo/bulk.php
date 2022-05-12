<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Url\Resolver\PageUrlResolver;

class Bulk extends DashboardPageController
{
    protected const DEFAULT_NUM_RESULTS = 10;

    protected const ALLOWED_NUM_RESULTS = [10, 25, 50, 100, 500];

    public function view()
    {
        if (!$this->app->make('helper/concrete/dashboard/sitemap')->canRead()) {
            $this->error->add(t("You don't have access to the sitemap"));
            $this->render('/dashboard/system/seo/bulk/no_access');

            return;
        }
        $searchRequest = $this->getSearchRequest();
        $this->set('pageSelector', $this->app->make(PageSelector::class));
        $this->set('allowedNumResults', static::ALLOWED_NUM_RESULTS);
        $this->set('searchRequest', $searchRequest);
        if (empty($searchRequest['search'])) {
            $this->set('pages', null);
            $this->set('pagination', '');
        } else {
            $pageList = $this->getRequestedSearchResults($searchRequest);
            $pagination = $pageList->getPagination();
            $pages = $this->serializePages($pagination->getCurrentPageResults());
            $this->set('pages', $pages);
            $this->set('pagination', $pagination->haveToPaginate() ? $pagination->renderView('dashboard') : '');
            if ($pages === []) {
                $this->error->add(t('No pages found.'));
            }
        }
    }

    public function saveRecord($cID = null)
    {
        $post = $this->request->request;
        $cID = (int) $cID;
        if (!$this->token->validate('save_seo_record_' . $cID)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $c = Page::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $metaTitle = trim($post->get('metaTitle'));
        if ($metaTitle !== '') {
            $titleFormat = $this->app->make('config')->get('concrete.seo.title_format');
            $siteName = $this->getSiteNameForPage($c);
            $autoTitle = sprintf($titleFormat, $siteName, $c->getCollectionName());
            if ($metaTitle === $autoTitle) {
                $metaTitle = '';
            }
        }
        if ($metaTitle === '') {
            $c->clearAttribute('meta_title');
        } else {
            $c->setAttribute('meta_title', $metaTitle);
        }
        $metaDescription = trim($post->get('metaDescription'));
        if ($metaDescription !== '') {
            $autoDescription = (string) $c->getCollectionDescription();
            if ($metaDescription === $autoDescription) {
                $metaDescription = '';
            }
        }
        if ($metaDescription === '') {
            $c->clearAttribute('meta_description');
        } else {
            $c->setAttribute('meta_description', $metaDescription);
        }
        if (!$c->isHomePage()) {
            $cHandle = trim($post->get('handle'));
            if ($cHandle !== '' && $cHandle !== $c->getCollectionHandle()) {
                $c->update(['cHandle' => $cHandle]);
                $c->rescanCollectionPath();
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($this->serializePage($c));
    }

    /**
     * Get the site name localized for a specific page.
     */
    protected function getSiteNameForPage(Page $page): string
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

    protected function getRequestedSearchResults(array $searchRequest): PageList
    {
        $pageList = new PageList();
        $pageList->setPageVersionToRetrieve(PageList::PAGE_VERSION_RECENT);
        $pageList->sortBy('cDateModified', 'desc');
        $query = $pageList->getQueryObject();
        if (trim($searchRequest['keywords']) !== '') {
            $pageList->filterByKeywords($searchRequest['keywords']);
        }
        $pageList->setItemsPerPage($searchRequest['numResults']);
        if ($searchRequest['cParentIDSearchField'] !== null) {
            $pc = Page::getByID($searchRequest['cParentIDSearchField']);
            if ($pc && !$pc->isError()) {
                if ($searchRequest['cParentAll']) {
                    $cPath = $pc->getCollectionPath();
		    $pageList->filterBySite($pc->getSite());
                    $pageList->filterByPath($cPath);
                } else {
                    $pageList->filterByParentID($searchRequest['cParentIDSearchField']);
                }
            }
        }
        if ($searchRequest['noDefaultDescription']) {
            $query->andWhere($query->expr()->orX(
                $query->expr()->isNull('cv.cvDescription'),
                $query->expr()->eq('cv.cvDescription', $query->createNamedParameter(''))
            ));
        }
        if ($searchRequest['noMetaDescription']) {
            $query->andWhere($query->expr()->orX(
                $query->expr()->isNull('csi.ak_meta_description'),
                $query->expr()->eq('csi.ak_meta_description', $query->createNamedParameter(''))
            ));
        }

        return $pageList;
    }

    protected function getSearchRequest(): array
    {
        $result = $this->request->request->all();
        if ($result === []) {
            $result = $this->request->query->all();
        }
        $result['keywords'] = (string) ($result['keywords'] ?? '');
        $numResults = (int) ($result['numResults'] ?? 0);
        $result['numResults'] = in_array($numResults, static::ALLOWED_NUM_RESULTS, true) ? $numResults : static::DEFAULT_NUM_RESULTS;
        $result['cParentIDSearchField'] = (int) ($result['cParentIDSearchField'] ?? 0) ?: null;
        $result['cParentAll'] = !empty($result['cParentAll']);
        $result['noDefaultDescription'] = !empty($result['noDefaultDescription']);
        $result['noMetaDescription'] = !empty($result['noMetaDescription']);

        return $result;
    }

    /**
     * Get the localized site name.
     */
    protected function getSiteNameForLocale(string $locale): string
    {
        static $names = [];
        if (!isset($names[$locale])) {
            $prevLocale = Localization::activeLocale();
            if ($prevLocale !== $locale) {
                Localization::changeLocale($locale);
            }
            $names[$locale] = tc('SiteName', $this->app->make('site')->getSite()->getSiteName());
            if ($prevLocale !== $locale) {
                Localization::changeLocale($prevLocale);
            }
        }

        return $names[$locale];
    }

    /**
     * @param \Concrete\Core\Page\Page[] $pages
     *
     * @return array
     */
    protected function serializePages(array $pages): array
    {
        $result = [];
        foreach ($pages as $page) {
            $result[] = $this->serializePage($page);
        }

        return $result;
    }

    protected function serializePage(Page $page): array
    {
        $isHomePage = $page->isHomePage();
        if (!$isHomePage) {
            $page->rescanCollectionPath();
        }
        $data = [
            'isHomePage' => $isHomePage,
            'cID' => $page->getCollectionID(),
            'name' => (string) $page->getCollectionName(),
            'type' => $page->getPageTypeName() ?: t('Single Page'),
            'handle' => (string) $page->getCollectionHandle(),
            'autoTitle' => sprintf($this->app->make('config')->get('concrete.seo.title_format'), $this->getSiteNameForPage($page), $page->getCollectionName()),
            'metaTitle' => (string) $page->getAttribute('meta_title'),
            'autoDescription' => (string) $page->getCollectionDescription(),
            'metaDescription' => (string) $page->getAttribute('meta_description'),
            'modified' => $page->getCollectionDateLastModified() ? $this->app->make('date')->formatDateTime($page->getCollectionDateLastModified()) : '',
            'url' => (string) $this->app->make(PageUrlResolver::class)->resolve([$page]),
            'saveAction' => (string) $this->action('saveRecord', $page->getCollectionID()),
            'savePayload' => [
                $this->token::DEFAULT_TOKEN_NAME => $this->token->generate('save_seo_record_' . $page->getCollectionID()),
            ],
        ];
        if ($isHomePage) {
            $data['htmlPath'] = '<strong class="collectionPath">/</strong>';
        } else {
            $path = $page->getCollectionPath();
            $tokens = explode('/', $path);
            $lastToken = array_pop($tokens);
            $tokens[] = '<strong class="collectionPath">' . $lastToken . '</strong>';
            $data['htmlPath'] = implode('/', $tokens);
        }

        $data['input'] = [
            'metaTitle' => $data['metaTitle'],
            'metaDescription' => $data['metaDescription'],
        ];
        if (!$isHomePage) {
            $data['input']['handle'] = $data['handle'];
        }

        return $data;
    }
}
