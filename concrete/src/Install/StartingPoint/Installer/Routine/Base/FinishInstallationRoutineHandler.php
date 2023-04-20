<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Renderer;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Production\Modes;
use Concrete\Core\Site\Service;

class FinishInstallationRoutineHandler implements InstallOptionsAwareInterface
{

    use InstallOptionsAwareTrait;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(Application $app, Service $siteService)
    {
        $this->app = $app;
        $this->siteService = $siteService;
    }

    public function __invoke()
    {
        $config = $this->app->make('config');
        $installConfiguration = $this->installOptions->getConfiguration();

        // Extract database config, and save it to database.php
        $database = $installConfiguration['database'];
        unset($installConfiguration['database']);

        $renderer = new Renderer($database);

        file_put_contents(DIR_CONFIG_SITE . '/database.php', $renderer->render());
        @chmod(DIR_CONFIG_SITE . '/database.php', $config->get('concrete.filesystem.permissions.file'));

        $siteConfig = $this->siteService->getDefault()->getConfigRepository();
        if (isset($installConfiguration['canonical-url']) && $installConfiguration['canonical-url']) {
            $siteConfig->save('seo.canonical_url', $installConfiguration['canonical-url']);
        }
        unset($installConfiguration['canonical-url']);
        if (isset($installConfiguration['canonical-url-alternative']) && $installConfiguration['canonical-url-alternative']) {
            $siteConfig->save('seo.canonical_url_alternative', $installConfiguration['canonical-url-alternative']);
        }
        unset($installConfiguration['canonical-url-alternative']);

        if (isset($installConfiguration['session-handler']) && $installConfiguration['session-handler']) {
            $config->save('concrete.session.handler', $installConfiguration['session-handler']);
        }
        unset($installConfiguration['session-handler']);

        $renderer = new Renderer($installConfiguration);
        if (!file_exists(DIR_CONFIG_SITE . '/app.php')) {
            file_put_contents(DIR_CONFIG_SITE . '/app.php', $renderer->render());
            @chmod(DIR_CONFIG_SITE . '/app.php', $config->get('concrete.filesystem.permissions.file'));
        }
        $config->save('app.server_timezone', $this->installOptions->getServerTimeZone(true)->getName());

        $config->save('concrete.security.production.mode', Modes::MODE_DEVELOPMENT);

        $this->installOptions->deleteFiles();

        // Set the version_db as the version_db_installed
        $config->save('concrete.version_db_installed', $config->get('concrete.version_db'));

        // Initiate announcements
        $announcementService = $this->app->make(AnnouncementService::class);
        $announcementService->createAnnouncementIfNotExists('collect_site_information');
        $announcementService->createAnnouncementIfNotExists('welcome');

        // Clear cache
        $config->clearCache();
        $this->app->make('cache')->flush();
    }


}
