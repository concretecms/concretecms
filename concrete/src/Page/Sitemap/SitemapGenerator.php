<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Cache\Cache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Sitemap\Element\SitemapFooter;
use Concrete\Core\Page\Sitemap\Element\SitemapHeader;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Concrete\Core\Page\Sitemap\Element\SitemapPageAlternativeLanguage;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use League\Url\Url;

defined('C5_EXECUTE') or die('Access Denied.');

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
     * When true (default), a page whose resolved URL belongs to a different host than
     * the canonical URL is treated as a bug and causes a RuntimeException.
     * Set to false to silently skip such pages instead (lenient mode).
     *
     * @var bool
     */
    protected $strictCanonicalHost = true;

    /**
     * @var \Concrete\Core\Page\Sitemap\PageListGenerator|null
     */
    private $pageListGenerator;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface|null
     */
    private $resolverManager;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\PageKey|false|null
     */
    private $sitemapChangeFrequencyAttributeKey = false;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\PageKey|false|null
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
     * @return $this;
     */
    public function setResolverManager(ResolverManagerInterface $resolverManager)
    {
        $this->resolverManager = $resolverManager;

        return $this;
    }

    /**
     * Generate sitemap elements scoped to a specific site.
     *
     * PAGE SCOPING — only pages that belong to this site are included:
     *   PageListGenerator::getSiteTreesIDList() collects every siteTreeID from the
     *   site's locales; generatePageList() then filters the Pages table with
     *   "WHERE siteTreeID IS NULL OR siteTreeID IN (<those IDs>)".
     *   The IS NULL arm captures legacy/global rows that carry no tree assignment;
     *   those pages are subsequently rejected by canIncludePageInSitemap() because
     *   they are system pages, in the trash, or otherwise not publicly visible.
     *   No page from a different site's tree can pass either condition.
     *
     * URL SCOPING — all <loc> entries use this site's canonical host:
     *   Pass $canonicalUrlOverride to force a specific base URL (e.g. in CLI or tests).
     *   When omitted, $site->getSiteCanonicalURL() is used.
     *   The instance-level customSiteCanonicalUrl property (setCustomSiteCanonicalUrl())
     *   is intentionally ignored here: it is mutable shared state that could have been
     *   set for a different site by a previous call, making the behaviour unpredictable.
     *   If a resolved URL escapes the canonical host, generateContents() throws (strict
     *   mode, default) or skips (lenient) — see setStrictCanonicalHost().
     *
     * SITEMAP INDEX (not implemented here):
     *   This method is the intended building block for sitemap index support. A caller
     *   iterating SiteService::getList() can invoke generateForSite() once per site,
     *   write each result to a separate file, and assemble a <sitemapindex> that
     *   references them. Because every call is anchored to one site's canonical host,
     *   cross-domain entries in the index are structurally impossible.
     *
     * @param string $canonicalUrlOverride optional base URL; overrides $site->getSiteCanonicalURL()
     *
     * @throws \RuntimeException if no canonical URL can be resolved for the site
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapElement[]|\Generator
     */
    public function generateForSite(Site $site, string $canonicalUrlOverride = ''): \Generator
    {
        // setSite() is what binds the SQL tree filter in PageListGenerator::generatePageList()
        // to this specific site — see PageListGenerator::getSiteTreesIDList().
        $pageListGenerator = $this->getPageListGenerator();
        $previousSite = $pageListGenerator->getSite();
        $pageListGenerator->setSite($site);

        // Derive the canonical URL from the explicit per-call override first, then
        // the site's own config. The instance-level customSiteCanonicalUrl is NOT
        // consulted: it is shared mutable state and could belong to a different site.
        $canonicalUrl = $canonicalUrlOverride !== '' ? $canonicalUrlOverride : $site->getSiteCanonicalURL();
        if ($canonicalUrl === '') {
            $pageListGenerator->setSite($previousSite);
            throw new \RuntimeException(sprintf(
                'Site "%s" has no canonical URL configured. Set one in SEO settings or pass it as the $canonicalUrlOverride argument.',
                $site->getSiteHandle()
            ));
        }

        $previousCustomUrl = $this->getCustomSiteCanonicalUrl();
        $this->setCustomSiteCanonicalUrl($canonicalUrl);
        try {
            yield from $this->generateContents();
        } finally {
            $pageListGenerator->setSite($previousSite);
            $this->setCustomSiteCanonicalUrl($previousCustomUrl);
        }
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
            $canonicalHost = $this->resolveCanonicalHost();
            yield $this->app->make(SitemapHeader::class, ['isMultilingual' => $multilingualEnabled]);
            foreach ($pageListGenerator->generatePageList() as $page) {
                $sitemapPage = $this->createSitemapPage($page, $multilingualEnabled);
                if ($canonicalHost !== '' && !$this->urlHostMatchesCanonical((string) $sitemapPage->getUrl(), $canonicalHost)) {
                    if ($this->strictCanonicalHost) {
                        throw new \RuntimeException(sprintf(
                            'Page %d resolved to a URL on a different host than the canonical URL "%s". '
                            . 'This indicates a misconfigured URL resolver or wrong-site page leakage. '
                            . 'Call setStrictCanonicalHost(false) to skip such pages instead of throwing.',
                            $page->getCollectionID(),
                            $canonicalHost
                        ));
                    }
                    continue;
                }
                yield $sitemapPage;
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
     * Return whether cross-domain URLs cause an exception (strict, default) or are silently skipped (lenient).
     */
    public function isStrictCanonicalHost(): bool
    {
        return $this->strictCanonicalHost;
    }

    /**
     * Control how cross-domain URL mismatches are handled during generation.
     *
     * Strict mode (default, true): throws \RuntimeException when a page's resolved URL
     * does not match the canonical host — surfaces misconfigured resolvers or wrong-site
     * page leakage as an explicit error rather than silently producing a corrupt sitemap.
     *
     * Lenient mode (false): silently skips offending pages instead of throwing.
     * Use this only as a temporary escape hatch while diagnosing resolver configuration.
     *
     * @return $this
     */
    public function setStrictCanonicalHost(bool $strict): self
    {
        $this->strictCanonicalHost = $strict;

        return $this;
    }

    /**
     * Resolve a sitemap file path into a full URL using the effective canonical URL.
     *
     * Uses the custom canonical URL if one has been set, otherwise falls back to the
     * site's configured `seo.canonical_url`. Returns the path unchanged when no
     * canonical URL is available (e.g. during testing or misconfigured installs).
     *
     * @param string $sitemapFile Relative path, e.g. `/sitemap-default.xml`.
     *
     * @return string Absolute URL, e.g. `https://example.com/sitemap-default.xml`.
     */
    public function resolveUrl(string $sitemapFile)
    {
        $siteConfig = $this->getPageListGenerator()->getSite()->getConfigRepository();
        $canonicalUrl = $this->getCustomSiteCanonicalUrl();
        if (!$canonicalUrl) {
            $canonicalUrl = $siteConfig->get('seo.canonical_url');
        }

        if ($canonicalUrl) {
            return rtrim($canonicalUrl, '/') . $sitemapFile;
        }

        return $sitemapFile;
    }

    /**
     * Derive the hostname from the effective canonical URL (custom override or site config).
     * Returns an empty string when no canonical URL is available.
     */
    protected function resolveCanonicalHost(): string
    {
        $url = $this->getCustomSiteCanonicalUrl();
        if ($url === '') {
            $site = $this->getPageListGenerator()->getSite();
            $url = $site !== null ? $site->getSiteCanonicalURL() : '';
        }

        return $url !== '' ? (string) (parse_url($url, PHP_URL_HOST) ?? '') : '';
    }

    /**
     * Return true when the given URL's host matches the canonical host (case-insensitive).
     * URLs without a host component (relative URLs) are considered matching.
     */
    protected function urlHostMatchesCanonical(string $url, string $canonicalHost): bool
    {
        $urlHost = (string) (parse_url($url, PHP_URL_HOST) ?? '');

        return $urlHost === '' || strcasecmp($urlHost, $canonicalHost) === 0;
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
     * @return \League\URL\URLInterface
     */
    protected function getPageUrl(Page $page)
    {
        return $this->getResolverManager()->resolve([$page]);
    }

    /**
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
     * @param bool $multilingualEnabled
     *
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapPage
     */
    protected function createSitemapPage(Page $page, $multilingualEnabled)
    {
        $result = new SitemapPage($page, $this->getPageUrl($page));
        $lasMod = $page->getCollectionDateLastModified();
        if ($lasMod) {
            $result->setLastModifiedAt(new \DateTime($lasMod));
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
                        if ($relatedPage && $pageListGenerator->canIncludePageInSitemap($relatedPage)) {
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
}
