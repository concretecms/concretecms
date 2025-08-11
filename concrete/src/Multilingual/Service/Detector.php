<?php

namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Session\SessionValidatorInterface;
use Concrete\Core\Site\SiteAggregateInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Strings;
use Punic\Misc;
use Symfony\Component\HttpFoundation\Session\Session;

defined('C5_EXECUTE') or die('Access Denied.');

class Detector implements ApplicationAwareInterface, SiteAggregateInterface
{
    use ApplicationAwareTrait;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var bool|Section
     */
    protected $preferredSection = false;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var RequestCache
     */
    protected $cache;

    /**
     * @param RequestCache $cache
     */
    public function __construct(RequestCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        if ($this->site === null) {
            $this->site = $this->app->make('site')->getSite();
        }

        return $this->site;
    }

    /**
     * Returns the Multilingual Page Section based on the Page object.
     *
     * @param Page $c
     *
     * @return Section|null
     */
    public function getActiveSection(Page $c): ?Section
    {
        if ($this->cache->isEnabled()) {
            $key = '/multilingual/detector/section/' . $c->getCollectionID();
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $locale = null;
        $al = Section::getBySectionOfSite($c);
        if ($al) {
            $locale = $al->getLocale();
        }
        if (!$locale) {
            $locale = Localization::activeLocale();
            $al = Section::getByLocale($locale);
        }

        if ($al) {
            if (isset($item) && $item->isMiss()) {
                $item->set($al);
                $this->cache->save($item);
            }

            return $al;
        }

        return null;
    }

    /**
     * Returns the locale string (e.g. en_US) based on the Page object.
     *
     * @param Page $c
     *
     * @return string
     */
    public function getActiveLocale(Page $c): string
    {
        if ($this->cache->isEnabled()) {
            $key = '/multilingual/detector/locale/' . $c->getCollectionID();
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $al = $this->getActiveSection($c);
        if ($al) {
            $locale = $al->getLocale();
        } else {
            $locale = Localization::activeLocale();
        }

        if (isset($item) && $item->isMiss()) {
            $item->set($locale);
            $this->cache->save($item);
        }

        return $locale;
    }

    /**
     * Returns all Multilingual Sections on the current site.
     *
     * @return Section[]
     */
    public function getAvailableSections(): array
    {
        if ($this->cache->isEnabled()) {
            $key = '/multilingual/detector/section/list';
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $sections = Section::getList($this->getSite());

        if (isset($item) && $item->isMiss()) {
            $item->set($sections);
            $this->cache->save($item);
        }

        return $sections;
    }

    /**
     * Returns a translated page based on the Page object.
     *
     * @param Page $currentPage
     * @param Section $targetSection
     *
     * @return Section|Page
     */
    public function getRelatedPage(Page $currentPage, Section $targetSection): Page
    {
        if ($this->cache->isEnabled()) {
            $key = '/multilingual/detector/link/' . $currentPage->getCollectionID() . '/' . $targetSection->getCollectionID();
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $relatedPage = null;
        if ($currentPage->isGeneratedCollection() && $currentPage->getSiteTreeID() == 0) {
            // If the current page is a single page, there is no translated page.
            $relatedPage = $currentPage;
        } else {
            $relatedID = $targetSection->getTranslatedPageID($currentPage);
            if ($relatedID) {
                $c = Page::getByID($relatedID);
                if ($c) {
                    $relatedPage = $c;
                }
            }
        }

        // If we fail to get a translated page, get the home page of the target language as a fallback.
        if (!$relatedPage) {
            $relatedPage = $targetSection;
        }

        if (isset($item) && $item->isMiss()) {
            $item->set($relatedPage);
            $this->cache->save($item);
        }

        return $relatedPage;
    }

    /**
     * Get a switch language link URL to redirect to the target section from the current page.
     *
     * @param mixed $currentPageID
     * @param mixed $targetSectionID
     */
    public function getSwitchLink($currentPageID, $targetSectionID): string
    {
        /** @var ResolverManagerInterface $resolver */
        $resolver = $this->app->make(ResolverManagerInterface::class);

        return (string) $resolver->resolve(['/ccm/frontend/multilingual/switch_language', $currentPageID, $targetSectionID]);
    }

    /**
     * Returns the preferred section based on session, cookie,
     * user object, default browser (if allowed), and finally
     * site preferences.
     * Since the user's language is not a locale but a language,
     * attempts to determine best section for the given language.
     *
     * @return Section|null
     */
    public function getPreferredSection()
    {
        if ($this->preferredSection === false) {
            /** @var Session $session */
            $session = $this->app->make('session');
            /** @var Strings $stringsValidator */
            $stringsValidator = $this->app->make(Strings::class);

            $section = null;
            $default_locale = null;
            if ($section === null) {
                $locale = false;
                // Detect locale by value stored in session or cookie
                if ($this->canSetSessionValue() && $session->has('multilingual_default_locale')) {
                    $locale = $session->get('multilingual_default_locale');
                } else {
                    $cookie = $this->app->make('cookie');
                    if ($cookie->has('multilingual_default_locale')) {
                        $locale = $cookie->get('multilingual_default_locale');
                    }
                }
                if ($locale) {
                    $home = Section::getByLocale($locale);
                    if (is_object($home)) {
                        $section = $home;
                        $default_locale = $locale;
                    }
                }
            }

            if ($section === null) {
                // Detect locale by user's preferred language
                $u = $this->app->make(User::class);
                if ($u->isRegistered()) {
                    $userDefaultLanguage = $u->getUserDefaultLanguage();
                    if ($userDefaultLanguage) {
                        $home = Section::getByLocaleOrLanguage($userDefaultLanguage);
                        if (is_object($home)) {
                            $section = $home;
                            $default_locale = $home->getLocale();
                        }
                    }
                }
            }

            if ($section === null) {
                // Detect locale by browsers headers
                $siteConfig = $this->getSite()->getConfigRepository();
                if ($siteConfig->get('multilingual.use_browser_detected_locale')) {
                    $browserLocales = Misc::getBrowserLocales();
                    foreach (array_keys($browserLocales) as $browserLocale) {
                        $home = Section::getByLocaleOrLanguage($browserLocale);
                        if (is_object($home)) {
                            $section = $home;
                            $default_locale = $browserLocale;
                            break;
                        }
                    }
                }
            }

            if ($section === null) {
                // Use the default site locale
                $locale = $this->getSite()->getDefaultLocale();
                if (is_object($locale)) {
                    $home = Section::getByLocale($locale->getLocale());
                    if ($home) {
                        $section = $home;
                        $default_locale = $locale->getLocale();
                    }
                }
            }

            if ($section !== null && $stringsValidator->notempty($default_locale) && $this->canSetSessionValue()) {
                $session->set('multilingual_default_locale', $default_locale);
            }

            $this->preferredSection = $section;
        }

        return $this->preferredSection;
    }

    /**
     * Set the locale associated to the 'site' localization context.
     *
     * @param Page $c The page to be used to determine the site locale (if null we'll use the current page)
     *
     * @throws \Exception
     */
    public function setupSiteInterfaceLocalization(?Page $c = null)
    {
        $loc = $this->app->make(Localization::class);
        $locale = null;
        if ($c === null) {
            $c = Page::getCurrentPage();
        }
        if ($c) {
            $pageController = $c->getPageController();
            if (is_object($pageController) && is_callable([$pageController, 'useUserLocale'])) {
                $useUserLocale = $pageController->useUserLocale();
            } else {
                $dh = $this->app->make('helper/concrete/dashboard');
                $useUserLocale = $dh->inDashboard($c);
            }
            if ($useUserLocale) {
                $u = $this->app->make(User::class);
                $locale = $u->getUserLanguageToDisplay();
            } else {
                if ($this->isEnabled()) {
                    $ms = Section::getBySectionOfSite($c);
                    if (!$ms) {
                        $ms = $this->getPreferredSection();
                    }
                    if ($ms) {
                        $locale = $ms->getLocale();

                        if ($this->canSetSessionValue()) {
                            $this->app->make('session')->set('multilingual_default_locale', $locale);
                        }
                    }
                }
                if (!$locale) {
                    $siteTree = $c->getSiteTreeObject();
                    if ($siteTree) {
                        $locale = $siteTree->getLocale()->getLocale();
                    }
                }
            }
        }
        if (!$locale) {
            $localeEntity = $this->getSite()->getDefaultLocale();
            if ($localeEntity) {
                $locale = $localeEntity->getLocale();
            }
        }
        if (!$locale) {
            $locale = $this->app->make('config')->get('concrete.locale');
        }
        $loc->setContextLocale(Localization::CONTEXT_SITE, $locale);
    }

    /**
     * Check if there's some multilingual section.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $result = false;
            if ($this->app->isInstalled()) {
                $site = $this->getSite();
                if (count($site->getLocales()) > 1) {
                    $result = true;
                }
            }
            $this->enabled = $result;
        }

        return $this->enabled;
    }

    /**
     * Assume that the site is multilingual enabled.
     *
     * @return $this
     */
    public function assumeEnabled()
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Check if we can set a session value.
     *
     * @return bool
     */
    protected function canSetSessionValue()
    {
        // If we already started the session, return true.
        if ($this->app->make(SessionValidatorInterface::class)->hasActiveSession()) {
            return true;
        }

        // If the site config value is true, it may start a new session without sign in to concrete5.
        $site = $this->getSite();
        if ($site !== null) {
            $siteConfig = $site->getConfigRepository();

            return (bool) $siteConfig->get('multilingual.always_track_user_locale');
        }

        return false;
    }
}
