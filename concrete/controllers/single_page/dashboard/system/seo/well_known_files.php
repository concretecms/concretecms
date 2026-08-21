<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Service\Configuration\HTTP\ApacheGenerator;
use Concrete\Core\Service\Configuration\HTTP\NginxGenerator;
use Concrete\Core\Site\WellKnown\WellKnownFileManager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Dashboard page: System > SEO & Statistics > Well-Known Files.
 *
 * Operates on the site currently chosen in the dashboard site switcher, which
 * DashboardSitePageController exposes through getSite(). Shows the status of that
 * site's sitemap.xml, robots.txt, llms.txt, security.txt, ads.txt, and humans.txt
 * and allows direct editing of all except sitemap.xml, which the Generate Sitemap
 * task writes. When more than one site has a canonical URL the page also shows the
 * nginx/Apache server configuration blocks needed to route well-known files
 * (including /.well-known/security.txt) per-domain.
 */
class WellKnownFiles extends DashboardSitePageController
{
    /**
     * The files listed in the status table, in display order.
     *
     * @var string[]
     */
    protected const FILENAMES = ['sitemap.xml', 'robots.txt', 'llms.txt', 'security.txt', 'ads.txt', 'humans.txt'];

    /**
     * Render the well-known files dashboard page for the active site.
     */
    public function view(): void
    {
        /** @var WellKnownFileManager $wellKnown */
        $wellKnown = $this->app->make(WellKnownFileManager::class);
        $site = $this->getSite();

        $this->set('form', $this->app->make(Form::class));
        // The manager decides where files are written using the same "more than one
        // site has a canonical URL" test, so the routing snippet is shown exactly when
        // files are stored per-host and the web server therefore needs to route them.
        $this->set('isMultisite', $wellKnown->isMultisite());
        $this->set('nginxConfig', $this->getNginxConfig());
        $this->set('apacheConfig', $this->getApacheConfig());

        $canEdit = $site !== null && $this->canManageWellKnownFiles();
        $this->set('canEdit', $canEdit);
        $this->set('siteName', $canEdit ? $site->getSiteName() : '');
        $this->set('canonicalUrl', $canEdit ? $site->getSiteCanonicalURL() : '');
        $this->set('files', $canEdit ? $this->getFileData($wellKnown, $site) : []);
        $this->set('content', $canEdit ? $this->getEditableContent($wellKnown, $site) : []);
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
     * Build the status table data for a site: generation state and public URL per file.
     *
     * @return array<string, array{exists: bool, lastModified: int|null, url: string}> Keyed by filename
     */
    protected function getFileData(WellKnownFileManager $wellKnown, Site $site): array
    {
        $hasCanonicalUrl = $site->getSiteCanonicalURL() !== '';
        $files = [];
        foreach (self::FILENAMES as $filename) {
            $path = $wellKnown->getFilePath($site, $filename);
            $exists = $path !== '' && is_file($path);
            $files[$filename] = [
                'exists' => $exists,
                'lastModified' => $exists ? (int) filemtime($path) : null,
                'url' => $hasCanonicalUrl ? $wellKnown->getUrlForSite($site, $filename) : '',
            ];
        }

        return $files;
    }

    /**
     * Read the current content of every editable well-known file for a site.
     *
     * @return array<string, string> Keyed by filename; '' when the file does not exist yet
     */
    protected function getEditableContent(WellKnownFileManager $wellKnown, Site $site): array
    {
        return [
            'robots.txt' => $this->readRobotsContent($wellKnown, $site),
            'llms.txt' => $this->readWellKnownContent($wellKnown, $site, 'llms.txt'),
            'security.txt' => $this->readWellKnownContent($wellKnown, $site, 'security.txt'),
            'ads.txt' => $this->readWellKnownContent($wellKnown, $site, 'ads.txt'),
            'humans.txt' => $this->readWellKnownContent($wellKnown, $site, 'humans.txt'),
        ];
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
     * The file is always written for the active site, so a posted form cannot target
     * a site other than the one the dashboard is currently editing.
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

        $site = $this->getSite();
        if ($site === null || !$this->canManageWellKnownFiles()) {
            $this->error->add(t('You do not have permission to manage well-known files.'));
        }

        $content = (string) $this->request->request->get('content');
        if (strlen($content) > 65536) {
            $this->error->add(t('The file content exceeds the maximum allowed size of 64 KB.'));
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
     * Whether the current user holds the Manage Well-Known Files task permission.
     *
     * Which site those files belong to is a separate question, answered by the dashboard
     * site switcher: it only offers sites the user can see (Permissions::canViewSiteInSelector(),
     * which resolves to sitemap-view rights on the site's home page). So this permission
     * governs the capability and the switcher governs its scope.
     *
     * Returns false when the permission key is missing, which happens only on an install
     * whose migrations have not been run — denying is the safe answer there.
     */
    protected function canManageWellKnownFiles(): bool
    {
        $pk = PermissionKey::getByHandle('manage_well_known_files');

        return $pk instanceof PermissionKey ? (bool) $pk->validate() : false;
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
