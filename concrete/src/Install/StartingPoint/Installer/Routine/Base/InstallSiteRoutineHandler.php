<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Site\Service;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;

class InstallSiteRoutineHandler implements InstallOptionsAwareInterface
{

    use InstallOptionsAwareTrait;

    /**
     * @var Service
     */
    protected $siteService;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Service $siteService, Application $app, Repository $config)
    {
        $this->siteService = $siteService;
        $this->app = $app;
        $this->config = $config;
    }

    public function __invoke()
    {
        $this->app->make('site/type')->installDefault();
        $site = $this->siteService->installDefault($this->installOptions->getSiteLocaleId());
        $site->getConfigRepository()->save('name', $this->installOptions->getSiteName());

        $uiLocaleId = $this->installOptions->getUiLocaleId();
        if ($uiLocaleId && $uiLocaleId !== Localization::BASE_LOCALE) {
            $this->config->save('concrete.locale', $uiLocaleId);
        }

        $this->config->save('concrete.version_installed', APP_VERSION);
        $this->config->save('concrete.misc.login_redirect', 'HOMEPAGE');

        $dbConfig = $this->app->make('config/database');
        $dbConfig->save('app.privacy_policy_accepted', $this->installOptions->isPrivacyPolicyAccepted());

    }


}
