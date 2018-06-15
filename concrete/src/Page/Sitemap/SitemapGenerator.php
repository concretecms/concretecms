<?php

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Sitemap\Element\SitemapFooter;
use Concrete\Core\Page\Sitemap\Element\SitemapHeader;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\Element\SitemapPageAlternativeLanguage;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use DateTime;

/**
 * Class to be used to generate the elements to be included in a sitemap.xml file.
 */
class SitemapGenerator
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $customSiteCanonicalUrl = '';

    /**
     * @var \Concrete\Core\Page\Sitemap\PageListGenerator|null
     */
    private $pageListGenerator;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface|null
     */
    private $resolverManager;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\PageKey|null|false
     */
    private $sitemapChangeFrequencyAttributeKey = false;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\PageKey|null|false
     */
    private $sitemapPriorityAttributeKey = false;

    /**
     * @var string|false
     */
    private $defaultChangeFrequency = false;

    /**
     * @var string|false
     */
    private $defaultPriority = false;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @return \Concrete\Core\Page\Sitemap\PageListGenerator
     */
    public function getPageListGenerator()
    {
        if ($this->pageListGenerator === null) {
            $this->pageListGenerator = $this->app->make(PageListGenerator::class);
        }

        return $this->pageListGenerator;
    }

    /**
     * @param \Concrete\Core\Page\Sitemap\PageListGenerator $pageListGenerator
     *
     * @return $this;
     */
    public function setPageListGenerator(PageListGenerator $pageListGenerator)
    {
        $this->pageListGenerator = $pageListGenerator;

        return $this;
    }

    /**
     * @return \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    public function getResolverManager()
    {
        if ($this->resolverManager === null) {
            $this->resolverManager = $this->app->make(ResolverManagerInterface::class);
        }

        return $this->resolverManager;
    }

    /**
     * @param \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
     *
     * @return $this;
     */
    public function setResolverManager(ResolverManagerInterface $resolverManager)
    {
        $this->pageListGenerator = $resolverManager;

        return $this;
    }

    /**
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapElement[]|\Generator
     */
    public function generateContents()
    {
        $pageListGenerator = $this->getPageListGenerator();
        $customCanonicalUrl = $this->getCustomSiteCanonicalUrl();
        if ($customCanonicalUrl !== '') {
            $siteConfig = $pageListGenerator->getSite()->getConfigRepository();
            $originalSiteCanonicalUrl = $siteConfig->get('seo.canonical_url');
            $siteConfig->set('seo.canonical_url', $customCanonicalUrl);
        }
        try {
            Cache::disableAll();
            $multilingualEnabled = $pageListGenerator->isMultilingualEnabled();
            yield new SitemapHeader($multilingualEnabled);
            foreach ($pageListGenerator->generatePageList() as $page) {
                yield $this->createSitemapPage($page, $multilingualEnabled);
            }
            yield new SitemapFooter();
        } finally {
            if ($customCanonicalUrl !== '') {
                $siteConfig->set('seo.canonical_url', $originalSiteCanonicalUrl);
            }
            Cache::enableAll();
        }
    }

    /**
     * Get the currently configured canonical URL of the site.
     *
     * @return string
     */
    public function getSiteCanonicalUrl()
    {
        $site = $this->getPageListGenerator()->getSite();
        if ($site === null) {
            $result = '';
        } else {
            $result = (string) $site->getConfigRepository()->get('seo.canonical_url');
        }

        return $result;
    }

    /**
     * Get the custom canonical URL for the site.
     *
     * @return string
     */
    public function getCustomSiteCanonicalUrl()
    {
        return $this->customSiteCanonicalUrl;
    }

    /**
     * Set the custom canonical URL for the site.
     *
     * @param string $customSiteCanonicalUrl
     *
     * @return $this
     */
    public function setCustomSiteCanonicalUrl($customSiteCanonicalUrl)
    {
        $this->customSiteCanonicalUrl = (string) $customSiteCanonicalUrl;

        return $this;
    }

    /**
     * Resolve an URL using the custom site canonical URL (if set).
     *
     * @param array $args
     *
     * @return \League\URL\URLInterface
     */
    public function resolveUrl(array $args)
    {
        return $this->withCustomCanonicalUrl(function () use ($args) {
            return $this->getResolverManager()->resolve($args);
        });
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey|null
     */
    protected function getSitemapChangeFrequencyAttributeKey()
    {
        if ($this->sitemapChangeFrequencyAttributeKey === false) {
            $category = $this->app->make(PageCategory::class);
            $this->sitemapChangeFrequencyAttributeKey = $category->getAttributeKeyByHandle('sitemap_changefreq');
        }

        return $this->sitemapChangeFrequencyAttributeKey;
    }

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey|null
     */
    protected function getSitemapPriorityAttributeKey()
    {
        if ($this->sitemapPriorityAttributeKey === false) {
            $category = $this->app->make(PageCategory::class);
            $this->sitemapPriorityAttributeKey = $category->getAttributeKeyByHandle('sitemap_priority');
        }

        return $this->sitemapPriorityAttributeKey;
    }

    /**
     * @return string
     */
    protected function getDefaultChangeFrequency()
    {
        if ($this->defaultChangeFrequency === false) {
            $this->defaultChangeFrequency = (string) $this->config->get('concrete.sitemap_xml.frequency');
        }

        return $this->defaultChangeFrequency;
    }

    /**
     * @return string
     */
    protected function getDefaultPriority()
    {
        if ($this->defaultPriority === false) {
            $this->defaultPriority = (string) $this->config->get('concrete.sitemap_xml.priority');
        }

        return $this->defaultPriority;
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     *
     * @return \League\URL\URLInterface
     */
    protected function getPageUrl(Page $page)
    {
        return $this->getResolverManager()->resolve([$page]);
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     *
     * @return string
     */
    protected function getPageChangeFrequency(Page $page)
    {
        $result = '';
        $ak = $this->getSitemapChangeFrequencyAttributeKey();
        if ($ak !== null) {
            $result = (string) $page->getAttribute($ak);
        }
        if ($result === '') {
            $result = $this->getDefaultChangeFrequency();
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     *
     * @return string
     */
    protected function getPagePriority(Page $page)
    {
        $result = '';
        $ak = $this->getSitemapPriorityAttributeKey();
        if ($ak !== null) {
            $result = (string) $page->getAttribute($ak);
        }
        if ($result === '') {
            $result = $this->getDefaultPriority();
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     * @param bool $multilingualEnabled
     *
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapPage
     */
    protected function createSitemapPage(Page $page, $multilingualEnabled)
    {
        $result = new SitemapPage($page, $this->getPageUrl($page));
        $lasMod = $page->getCollectionDateLastModified();
        if ($lasMod) {
            $result->setLastModifiedAt(new DateTime($lasMod));
        }
        $result
            ->setChangeFrequency($this->getPageChangeFrequency($page))
            ->setPriority($this->getPagePriority($page))
        ;

        if ($multilingualEnabled) {
            $this->populateLanguageAlternatives($result);
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Page\Sitemap\Element\SitemapPage $sitemapPage
     */
    protected function populateLanguageAlternatives(SitemapPage $sitemapPage)
    {
        $pageListGenerator = $this->getPageListGenerator();
        $page = $sitemapPage->getPage();
        $pageSection = $pageListGenerator->getMultilingualSectionForPage($page);
        if ($pageSection !== null) {
            $addThisPage = false;
            foreach ($pageListGenerator->getMultilingualSections() as $relatedSection) {
                if ($relatedSection !== $pageSection) {
                    $relatedPageID = $relatedSection->getTranslatedPageID($page);
                    if ($relatedPageID) {
                        $relatedPage = Page::getByID($relatedPageID);
                        if ($relatedPage || $pageListGenerator->canIncludePageInSitemap($relatedPage)) {
                            $relatedUrl = $this->getPageUrl($relatedPage);
                            $sitemapPage->addAlternativeLanguage(new SitemapPageAlternativeLanguage($relatedSection, $relatedPage, $relatedUrl));
                            $addThisPage = true;
                        }
                    }
                }
            }
            if ($addThisPage) {
                $sitemapPage->addAlternativeLanguage(new SitemapPageAlternativeLanguage($pageSection, $page, clone $sitemapPage->getUrl()));
            }
        }
    }

    /**
     * Don't use with generators!
     *
     * @param callable $run
     */
    protected function withCustomCanonicalUrl(callable $run)
    {
        $customCanonicalUrl = $this->getCustomSiteCanonicalUrl();
        if ($customCanonicalUrl !== '') {
            $siteConfig = $this->getPageListGenerator()->getSite()->getConfigRepository();
            $originalSiteCanonicalUrl = $siteConfig->get('seo.canonical_url');
            $siteConfig->set('seo.canonical_url', $customCanonicalUrl);
        }
        try {
            return $run();
        } finally {
            if ($customCanonicalUrl !== '') {
                $siteConfig->set('seo.canonical_url', $originalSiteCanonicalUrl);
            }
        }
    }
}
