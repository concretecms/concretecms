<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker as PermissionChecker;
use Concrete\Core\Service\Configuration\HTTP\ApacheGenerator;
use Concrete\Core\Service\Configuration\HTTP\NginxGenerator;
use Concrete\Core\Site\Service as SiteService;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Dashboard page: System > SEO & Statistics > Well-Known Files.
 *
 * Shows the status of per-site sitemap.xml, robots.txt, llms.txt, security.txt,
 * ads.txt, and humans.txt files and allows direct editing of all except sitemap.xml.
 * For multisite installs the page also shows the nginx/Apache server configuration
 * blocks needed to route well-known files (including /.well-known/security.txt)
 * per-domain.
 */
class WellKnownFiles extends DashboardPageController
{
    /**
     * Render the well-known files dashboard page.
     */
    public function view(): void
    {
        $siteService = $this->app->make(SiteService::class);
        /** @var WellKnownFileManager $wellKnown */
        $wellKnown = $this->app->make(WellKnownFileManager::class);

        $siteData = [];
        $sitesWithCanonical = 0;
        foreach ($siteService->getList() as $site) {
            if (!$this->canAdminSite($site)) {
                continue;
            }

            $canonicalUrl = $site->getSiteCanonicalURL();

            if ($canonicalUrl !== '') {
                $sitesWithCanonical++;
            }

            $siteData[] = [
                'id' => $site->getSiteID(),
                'handle' => $site->getSiteHandle(),
                'name' => $site->getSiteName(),
                'canonicalUrl' => $canonicalUrl,
                'sitemapUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'sitemap.xml') : '',
                'robotsUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'robots.txt') : '',
                'llmsUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'llms.txt') : '',
                'securityUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'security.txt') : '',
                'adsUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'ads.txt') : '',
                'humansUrl' => $canonicalUrl !== '' ? $wellKnown->getUrlForSite($site, 'humans.txt') : '',
                'sitemap' => $this->fileStatus($wellKnown, $site, 'sitemap.xml'),
                'robots' => $this->fileStatus($wellKnown, $site, 'robots.txt'),
                'llms' => $this->fileStatus($wellKnown, $site, 'llms.txt'),
                'security' => $this->fileStatus($wellKnown, $site, 'security.txt'),
                'ads' => $this->fileStatus($wellKnown, $site, 'ads.txt'),
                'humans' => $this->fileStatus($wellKnown, $site, 'humans.txt'),
                'robotsContent' => $this->readRobotsContent($wellKnown, $site),
                'llmsContent' => $this->readWellKnownContent($wellKnown, $site, 'llms.txt'),
                'securityContent' => $this->readWellKnownContent($wellKnown, $site, 'security.txt'),
                'adsContent' => $this->readWellKnownContent($wellKnown, $site, 'ads.txt'),
                'humansContent' => $this->readWellKnownContent($wellKnown, $site, 'humans.txt'),
            ];
        }

        $this->set('siteData', $siteData);
        // $sitesWithCanonical counts only sites visible to this admin (canAdminSite filter).
        // A restricted admin who manages only one of several sites won't see the nginx/Apache
        // routing snippet — they don't need it and shouldn't be expected to apply it.
        // WellKnownFileManager uses the same "> 1" threshold but counts all sites system-wide
        // to decide where to write files, since file routing is an infrastructure concern that
        // must reflect the actual system topology regardless of who is logged in.
        $this->set('isMultisite', $sitesWithCanonical > 1);
        $this->set('nginxConfig', $this->getNginxConfig());
        $this->set('apacheConfig', $this->getApacheConfig());
        $this->set('form', $this->app->make(Form::class));
    }

    public function save_robots(): void
    {
        $this->saveWellKnownFile('robots.txt', 'save_robots_txt', t('robots.txt saved successfully.'));
    }

    public function save_llms(): void
    {
        $this->saveWellKnownFile('llms.txt', 'save_llms_txt', t('llms.txt saved successfully.'));
    }

    /**
     * The file is stored flat as security.txt in the per-site directory; the web
     * server routes /.well-known/security.txt to it. In single-site mode it is written
     * directly to .well-known/security.txt in the webroot.
     */
    public function save_security(): void
    {
        $this->saveWellKnownFile('security.txt', 'save_security_txt', t('security.txt saved successfully.'));
    }

    public function save_ads(): void
    {
        $this->saveWellKnownFile('ads.txt', 'save_ads_txt', t('ads.txt saved successfully.'));
    }

    public function save_humans(): void
    {
        $this->saveWellKnownFile('humans.txt', 'save_humans_txt', t('humans.txt saved successfully.'));
    }

    /**
     * @param string $filename One of the allowed well-known filenames
     *
     * @return array{exists: bool, lastModified: int|null}
     */
    protected function fileStatus(WellKnownFileManager $wellKnown, Site $site, string $filename): array
    {
        $path = $wellKnown->getFilePath($site, $filename);
        $exists = $path !== '' && is_file($path);

        return ['exists' => $exists, 'lastModified' => $exists ? (int) filemtime($path) : null];
    }

    /**
     * Prefers the managed well-known file; falls back to the webroot robots.txt
     * as a useful starting point when the file has not yet been generated via the dashboard.
     *
     * @return string File contents, or '' if neither file exists
     */
    protected function readRobotsContent(WellKnownFileManager $wellKnown, Site $site): string
    {
        $path = $wellKnown->getFilePath($site, 'robots.txt');
        if ($path !== '' && is_file($path)) {
            return (string) file_get_contents($path);
        }

        $webroot = rtrim(DIR_BASE, '/') . '/robots.txt';

        return is_file($webroot) ? (string) file_get_contents($webroot) : '';
    }

    /**
     * Return the current content of a well-known file, or '' if it does not yet exist.
     *
     * @param string $filename One of the allowed well-known filenames
     *
     * @return string File contents, or ''
     */
    protected function readWellKnownContent(WellKnownFileManager $wellKnown, Site $site, string $filename): string
    {
        $path = $wellKnown->getFilePath($site, $filename);
        if ($path !== '' && is_file($path)) {
            return (string) file_get_contents($path);
        }

        return '';
    }

    /**
     * Validate, write, and redirect for a well-known file save action.
     *
     * @param string $filename One of the allowed well-known filenames
     * @param string $tokenName CSRF token action name
     * @param string $successMessage Flash message shown on successful save
     */
    protected function saveWellKnownFile(string $filename, string $tokenName, string $successMessage): void
    {
        if (!$this->token->validate($tokenName)) {
            $this->error->add($this->token->getErrorMessage());
            $this->view();

            return;
        }

        $siteID = (int) $this->request->request->get('siteID');
        $content = (string) $this->request->request->get('content');

        if (strlen($content) > 65536) {
            $this->error->add(t('The file content exceeds the maximum allowed size of 64 KB.'));
        }

        $siteService = $this->app->make(SiteService::class);
        $site = $siteID > 0 ? $siteService->getByID($siteID) : null;
        if ($site === null || !$this->canAdminSite($site)) {
            $this->error->add(t('Site not found or you do not have permission to edit its files.'));
        }

        if ($this->error->has()) {
            $this->view();

            return;
        }

        /** @var WellKnownFileManager $wellKnown */
        $wellKnown = $this->app->make(WellKnownFileManager::class);
        $path = $wellKnown->writeFile($site, $filename, $content);

        if ($path === '') {
            $this->error->add(t('Could not save %s.', $filename));
            $this->view();

            return;
        }

        $this->flash('success', $successMessage);
        $this->buildRedirect($this->action())->send();
        exit;
    }

    /**
     * Uses canAdminPage() on the site's home page — the standard ConcreteCMS signal
     * that a user has full administrative rights over a site tree.
     */
    protected function canAdminSite(Site $site): bool
    {
        $homePage = $site->getSiteHomePageObject();
        if ($homePage === null) {
            return false;
        }

        return (bool) (new PermissionChecker($homePage))->canAdminPage();
    }

    /**
     * @return string nginx location block for well-known files routing, or '' if not available
     */
    protected function getNginxConfig(): string
    {
        $rule = $this->app->make(NginxGenerator::class)->getRule('well_known_files');

        return $rule !== null ? $rule->getCode() : '';
    }

    /**
     * @return string Apache .htaccess rules for well-known files routing, or '' if not available
     */
    protected function getApacheConfig(): string
    {
        $rule = $this->app->make(ApacheGenerator::class)->getRule('well_known_files');

        return $rule !== null ? $rule->getCode() : '';
    }
}
