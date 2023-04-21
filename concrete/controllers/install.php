<?php

namespace Concrete\Controller;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Install\Command\ValidateEnvironmentCommand;
use Concrete\Core\Install\ExecutedPreconditionList;
use Concrete\Core\Install\InstallEnvironment;
use Concrete\Core\Install\Installer;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\InstallerOptionsFactory;
use Concrete\Core\Install\PreconditionService;
use Concrete\Core\Install\StartingPointService;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteTranslationsProvider;
use Concrete\Core\View\View;
use Punic\Comparer as PunicComparer;
use Symfony\Component\HttpFoundation\JsonResponse;

defined('C5_EXECUTE') or die('Access Denied.');

class Install extends Controller
{

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::getViewObject()
     */
    public function getViewObject()
    {
        $v = new View('/frontend/install');
        $v->setViewTheme('concrete');
        return $v;
    }

    /**
     * @return array
     */
    protected function getLocales()
    {
        $localLocales = Localization::getAvailableInterfaceLanguageDescriptions(null);

        $coreVersion = $this->app->make('config')->get('concrete.version_installed');
        $rtp = $this->app->make(RemoteTranslationsProvider::class);
        // We may be offline, so let's ignore connection issues
        try {
            $remoteLocaleStats = $rtp->getAvailableCoreStats($coreVersion);
        } catch (\Exception $x) {
            $remoteLocaleStats = [];
        }
        $remoteLocales = [];
        foreach (array_keys($remoteLocaleStats) as $remoteLocaleID) {
            if (!isset($localLocales[$remoteLocaleID])) {
                $remoteLocales[$remoteLocaleID] = Localization::getLanguageDescription($remoteLocaleID, null);
            }
        }
        $comparer = new PunicComparer();
        $comparer->sort($remoteLocales, true);
        if (empty($localLocales) && !empty($remoteLocales)) {
            $localLocales = [
                Localization::BASE_LOCALE => Localization::getLanguageDescription(Localization::BASE_LOCALE, null),
            ];
        }

        return [$localLocales, $remoteLocales];
    }

    public function view(string $locale = null)
    {
        if ($locale) {
            Localization::changeLocale($locale);
            $this->set('preconditions', $this->getPreconditions());
            $this->set('startingPoints', $this->app->make(StartingPointService::class)->getStartingPoints());
            $this->set('locale', $locale);
        }
        $config = $this->app->make('config');
        list($locales, $onlineLocales) = $this->getLocales();
        $this->set('locales', $locales);
        $this->set('onlineLocales', $onlineLocales);
        $this->set('concreteVersion', $config->get('concrete.version'));
        $this->requireAsset('core/installer');
        $this->set('lang', $this->getStrings());
        $ll = $this->app->make('localization/languages');
        $languages = $ll->getLanguageList();
        $this->set('languages', $languages);
        $chunks = explode('_', Localization::activeLocale());
        $this->set('siteLocaleLanguage', $chunks[0]);
        $cl = $this->app->make('lists/countries');
        $this->set('countries', $cl->getCountries());
        $this->set('siteLocaleCountry', $chunks[1] ?? null);
        $this->set('timezone', @date_default_timezone_get() ?: 'UTC');
        $this->set('timezones', $this->app->make('date')->getTimezones());
    }

    public function on_start()
    {
        Cache::disableAll();
    }

    public function run_routine($pkgHandle)
    {
        $options = $this->app->make(InstallerOptions::class);
        $options->load();
        $options->setStartingPointHandle($pkgHandle);
        $r = [];
        try {
            $installer = $this->app->make(Installer::class);
            $installer->setOptions($options);
            $routine = $installer->getRoutineFromRequest();
            $installer->executeRoutine($routine);
            $r['error'] = false;
        } catch (\Exception $e) {
            $r['error'] = true;
            $r['message'] = tc('InstallError', '%s.<br><br>Trace:<br>%s', $e->getMessage(), $e->getTraceAsString());
            $options->deleteFiles();
        }

        return new JsonResponse($r);
    }

    public function begin_installation()
    {
        $environment = $this->loadEnvironmentFromRequest();
        $options = $this->app->make(InstallerOptionsFactory::class)->createFromEnvironment($environment);
        $options->save();

        $installer = $this->app->make(Installer::class);
        $installer->setOptions($options);
        $startingPoint = $installer->getStartingPoint(true);

        $commands = $startingPoint->getInstaller()->getInstallCommands($options);
        return $installer->sendCommandsToClient($commands);
    }

    protected function getPreconditions(): ExecutedPreconditionList
    {
        $service = $this->app->make(PreconditionService::class);
        $list = $service->getPreconditionList();
        return $list;
    }

    public function reloadPreconditions()
    {
        return new JsonResponse($this->getPreconditions());
    }

    public function getInstallerStrings(string $locale): JsonResponse
    {
        Localization::changeLocale($locale);
        $data = [];
        $data['i18n'] = $this->getStrings();
        $data['preconditions'] = $this->getPreconditions();
        $data['starting_points'] = $this->app->make(StartingPointService::class)->getStartingPoints();
        return new JsonResponse($data);
    }

    public function web_precondition($handle, $argument = '')
    {
        $service = $this->app->make(PreconditionService::class);
        $precondition = $service->getPreconditionByHandle($handle);
        if (!$precondition instanceof WebPreconditionInterface) {
            throw new \Exception(sprintf('%s is not a valid precondition handle', $handle));
        }
        $result = $precondition->getAjaxAnswer($argument);
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->json($result);
    }

    protected function getStrings()
    {
        $config = $this->app->make('config');
        $lang = [
            'title' => t('Install Concrete CMS'),
            'chooseLanguage' => t('Please select your language.'),
            'stepLanguage' => t('Choose Language'),
            'stepEnvironment' => t('Environment'),
            'stepRequirements' => t('System Requirements'),
            'stepContent' => t('Site Content'),
            'stepConfirm' => t('Confirm Installation'),
            'stepPerformInstallation' => t('Installation in Progress'),
            'stepInstallationComplete' => t('Installation Complete'),
            'installationCompleteMessage' => t(
                'Concrete has been installed. You have been logged in as <b>%s</b> with the password you chose. If you wish to change this password, you may do so from the users area of the dashboard.',
                USER_SUPER
            ),
            'installedLanguages' => t('Installed Languages'),
            'availableLanguages' => t('Available Languages'),
            'select' => t('Select'),
            'selected' => t('Selected'),
            'requiredPreconditions' => t('Required'),
            'optionalPreconditions' => t('Optional'),
            'installErrors' => t(
                'There are problems with your installation environment. Please correct them and click the button below to re-run the pre-installation tests.'
            ),
            'installErrorsTrouble' => t(
                'Having trouble? Check the <a href="%s">installation help forums</a>, or <a href="%s">have us host a copy</a> for you.',
                'https://forums.concretecms.org',
                'https://www.concretecms.com/'
            ),
            'runTestsAgain' => t('Run Tests Again'),
            'back' => t('Back'),
            'next' => t('Next'),
            'site' => t('Site'),
            'siteName' => t('Site Name'),
            'email' => t('Email'),
            'password' => t('Administrator Password'),
            'confirmPassword' => t('Confirm Password'),
            'database' => t('Database'),
            'dbServer' => t('Server'),
            'dbUsername' => t('MySQL Username'),
            'dbPassword' => t('MySQL Password'),
            'dbDatabase' => t('Database'),
            'privacyPolicy' => t('Privacy Policy'),
            'privacyPolicyExplanation' => t(
                'Concrete CMS collects some information about your website to assist in upgrading and checking add-on compatibility. This information can be disabled in configuration.'
            ),
            'privacyPolicyLabel' => t(
                'Yes, I understand and agree to the <a target="_blank" href="%s">Privacy Policy</a>.',
                $config->get('concrete.urls.privacy_policy')
            ),
            'editYourSite' => t('Edit Your Site'),
            'installationComplete' => t('Installation complete.'),
            'startingPoint' => t('Starting Point'),
            'otherStartingPoints' => t('Other Starting Points'),
            'advancedOptions' => t('Advanced Options'),
            'urls' => t('URLS & Session'),
            'urlPlaceholder' => t('%s or %s', 'http://...', 'https://...'),
            'mainCanonicalUrl' => t('Canonical URL'),
            'alternativeCanonicalUrl' => t('Alternative Canonical URL'),
            'sessionHandler' => t('Session Handler'),
            'sessionHandlerDefault' => t('Default Handler (Recommended)'),
            'sessionHandlerDatabase' => t('Database'),
            'locale' => t('Concrete CMS Locale'),
            'language' => t('Site Locale Language'),
            'country' => t('Site Locale Country'),
            'timezone' => t('Time Zone'),
            'ignoreWarnings' => t('Ignore warnings and proceed with installation.'),
            'loadingInstallationRoutines' => t('Loading installation routines...'),
            'interstitial' => [
                'whileYouWait' => t('While You Wait'),
                'forums' => t('Forums'),
                'forumsMessage' => t(
                    '<a href="%s" target="_blank">The forums</a> on concretecms.org are full of helpful community members that make Concrete so great.',
                    $config->get('concrete.urls.help.forum')
                ),
                'userDocumentation' => t('User Documentation'),
                'userDocumentationMessage' => t(
                    'Read the <a href="%s" target="_blank">User Documentation</a> to learn editing and site management with Concrete CMS.',
                    $config->get('concrete.urls.help.user')
                ),
                'screencasts' => t('Screencasts'),
                'screencastsMessage' => t(
                    'The Concrete <a href="%s" target="_blank">YouTube Channel</a> is full of useful videos covering how to use Concrete CMS.',
                    $config->get('concrete.urls.videos')
                ),
                'developerDocumentation' => t('Developer Documentation'),
                'developerDocumentationMessage' => t(
                    'The <a href="%s" target="_blank">Developer Documentation</a> covers theming, building add-ons and custom Concrete development.',
                    $config->get('concrete.urls.help.developer')
                )
            ],
            'confirm' => [
                'site' => t('Site'),
                'content' => t('Site Content'),
                'database' => t('Database'),
                'adminUser' => t('Administrator User'),
                'session' => t('Session'),
                'localization' => t('Localization'),
                'beginInstallation' => t('Install Concrete CMS'),
            ],
        ];

        return $lang;
    }

    protected function loadEnvironmentFromRequest(): InstallEnvironment
    {
        $data = $this->request->request->all();
        $environment = new InstallEnvironment();
        $environment->setLocale($data['locale']);
        $environment->setStartingPoint($data['startingPoint']);
        $environment->setSiteName($data['site']['name'] ?? '');
        $environment->setEmail($data['adminUser']['email'] ?? '');
        $environment->setPassword($data['adminUser']['password'] ?? '');
        $environment->setConfirmPassword($data['adminUser']['confirmPassword'] ?? '');
        $environment->setDbServer($data['database']['server'] ?? '');
        $environment->setDbUsername($data['database']['username'] ?? '');
        $environment->setDbPassword($data['database']['password'] ?? '');
        $environment->setDbDatabase($data['database']['database'] ?? '');
        $environment->setAcceptPrivacyPolicy($data['site']['privacyPolicy'] ?? false);
        if (!empty($data['site']['hasCanonicalUrl'])) {
            $environment->setCanonicalUrl($data['site']['canonicalUrl'] ?? '');
        }
        if (!empty($data['site']['hasAlternativeCanonicalUrl'])) {
            $environment->setAlternativeCanonicalUrl($data['site']['alternativeCanonicalUrl'] ?? '');
        }
        $environment->setSessionHandler($data['session']['handler'] ?? '');
        $environment->setSiteLocaleLanguage($data['localization']['siteLocaleLanguage'] ?? '');
        $environment->setSiteLocaleCountry($data['localization']['siteLocaleCountry'] ?? '');
        $environment->setTimezone($data['localization']['timezone'] ?? '');
        return $environment;
    }

    public function validate_environment(): JsonResponse
    {
        $command = new ValidateEnvironmentCommand($this->loadEnvironmentFromRequest());
        $response = $this->app->executeCommand($command);
        return new JsonResponse($response);
    }
}