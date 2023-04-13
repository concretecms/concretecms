<?php

namespace Concrete\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Install\ExecutedPreconditionList;
use Concrete\Core\Install\InstallEnvironment;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionService;
use Concrete\Core\Install\Command\ValidateEnvironmentCommand;
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
            'chooseLanguage' => t('Choose the language you want to run your site in.'),
            'stepLanguage' => t('Choose Language'),
            'stepEnvironment' => t('Environment'),
            'stepRequirements' => t('System Requirements'),
            'installedLanguages' => t('Installed Languages'),
            'availableLanguages' => t('Available Languages'),
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
            'advancedOptions' => t('Advanced Options'),
            'urls' => t('URLS & Session'),
            'urlPlaceholder' => t('%s or %s', 'http://...', 'https://...'),
            'mainCanonicalUrl' => t('Set main canonical URL'),
            'alternativeCanonicalUrl' => t('Set alternative canonical URL'),
            'sessionHandler' => t('Session Handler'),
            'sessionHandlerDefault' => t('Default Handler (Recommended)'),
            'sessionHandlerDatabase' => t('Database'),
            'language' => t('Language'),
            'country' => t('Country'),
            'timezone' => t('Time Zone'),
    ];

        return $lang;
    }

    public function validate_environment(): JsonResponse
    {
        $post = $this->request->request;
        $environment = new InstallEnvironment();
        $environment->setLocale($post->get('locale'));
        $environment->setSiteName($post->get('siteName'));
        $environment->setEmail($post->get('email'));
        $environment->setPassword($post->get('password'));
        $environment->setConfirmPassword($post->get('confirmPassword'));
        $environment->setDbServer($post->get('dbServer'));
        $environment->setDbUsername($post->get('dbUsername'));
        $environment->setDbPassword($post->get('dbPassword'));
        $environment->setDbDatabase($post->get('dbDatabase'));
        $environment->setAcceptPrivacyPolicy($post->get('privacyPolicy'));
        if ($post->get('hasCanonicalUrl')) {
            $environment->setCanonicalUrl($post->get('canonicalUrl'));
        }
        if ($post->get('hasAlternativeCanonicalUrl')) {
            $environment->setAlternativeCanonicalUrl($post->get('alternativeCanonicalUrl'));
        }
        $environment->setSessionHandler($post->get('sessionHandler'));
        $environment->setSiteLocaleLanguage($post->get('siteLocaleLanguage'));
        $environment->setSiteLocaleCountry($post->get('siteLocaleCountry'));
        $environment->setTimezone($post->get('timezone'));
        $command = new ValidateEnvironmentCommand($environment);
        $response = $this->app->executeCommand($command);
        return new JsonResponse($response);
    }
}