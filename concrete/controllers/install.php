<?php
namespace Concrete\Controller;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Install\Installer;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionService;
use Concrete\Core\Install\WebPreconditionInterface;
use Concrete\Core\Localization\Localization as Localization;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteTranslationsProvider;
use Concrete\Core\Url\UrlImmutable;
use Controller;
use Database;
use Exception;
use Hautelook\Phpass\PasswordHash;
use Punic\Comparer as PunicComparer;
use StartingPointPackage;
use stdClass;
use View;

defined('C5_EXECUTE') or die('Access Denied.');

class Install extends Controller
{
    public $helpers = ['form', 'html'];

    public function getViewObject()
    {
        $v = new View('/frontend/install');
        $v->setViewTheme('concrete');

        return $v;
    }

    /**
     * The installer instance.
     *
     * @var Installer|null
     */
    private $installer = null;

    /**
     * Get the installer instance.
     *
     * @return Installer
     */
    protected function getInstaller()
    {
        if ($this->installer === null) {
            $this->installer = $this->app->make(Installer::class);
        }

        return $this->installer;
    }

    /**
     * Get the options used by the installer.
     *
     * @return \Concrete\Core\Install\InstallerOptions
     */
    protected function getInstallerOptions()
    {
        return $this->getInstaller()->getOptions();
    }

    public function view()
    {
        $this->set('backgroundFade', 500);
        if ($this->getInstallerOptions()->hasConfigurationFiles()) {
            $this->testAndRunInstall();
        } else {
            list($locales, $onlineLocales) = $this->getLocales();
            $this->set('locales', $locales);
            $this->set('onlineLocales', $onlineLocales);
        }
    }

    public function select_language()
    {
        $localeID = $this->request->request->get('wantedLocale');
        if ($localeID) {
            if ($localeID !== Localization::BASE_LOCALE) {
                $localLocales = Localization::getAvailableInterfaceLanguageDescriptions(null);
                if (!isset($localLocales[$localeID])) {
                    $ti = $this->app->make(TranslationsInstaller::class);
                    /* @var TranslationsInstaller $ti */
                    try {
                        $ti->installCoreTranslations($localeID);
                    } catch (Exception $x) {
                        $this->set('error', $x);
                        $this->view();

                        return;
                    }
                }
            }
            $this->set('locale', $localeID);
            Localization::changeLocale($localeID);
        }
    }

    protected function getLocales()
    {
        $localLocales = Localization::getAvailableInterfaceLanguageDescriptions(null);

        $coreVersion = $this->app->make('config')->get('concrete.version_installed');
        $rtp = $this->app->make(RemoteTranslationsProvider::class);
        /* @var RemoteTranslationsProvider $rtp */
        // We may be offline, so let's ignore connection issues
        try {
            $remoteLocaleStats = $rtp->getAvailableCoreStats($coreVersion);
        } catch (Exception $x) {
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

    protected function testAndRunInstall()
    {
        $e = $this->app->make('helper/validation/error');
        try {
            $installerOptions = $this->getInstallerOptions();
            $installerOptions->load();
            $uiLocaleId = $installerOptions->getUiLocaleId();
            if ($uiLocaleId !== '') {
                Localization::changeLocale($uiLocaleId);
            }
            $e->add($this->getInstaller()->checkOptions());
        } catch (UserMessageException $x) {
            $e->add($x);
        }
        if ($e->has()) {
            $this->set('error', $e);
        } else {
            $this->set('backgroundFade', 0);
            $spl = $this->getInstaller()->getStartingPoint(true);
            $this->set('installPackage', $spl->getPackageHandle());
            $this->set('installRoutines', $spl->getInstallRoutines());
            $this->set(
                'successMessage',
                t(
                    'concrete5 has been installed. You have been logged in as <b>%s</b> with the password you chose. If you wish to change this password, you may do so from the users area of the dashboard.',
                    USER_SUPER
                )
            );
        }
    }

    public function setup()
    {
        $config = $this->app['config'];
        $passwordMinLength = (int) $config->get('concrete.user.password.minimum', 5);
        $passwordMaxLength = (int) $config->get('concrete.user.password.maximum');
        $passwordAttributes = [
            'autocomplete' => 'off',
        ];
        if ($passwordMinLength > 0) {
            $passwordAttributes['required'] = 'required';
            if ($passwordMaxLength > 0) {
                $passwordAttributes['placeholder'] = t('Between %1$s and %2$s Characters', $passwordMinLength, $passwordMaxLength);
                $passwordAttributes['pattern'] = '.{' . $passwordMinLength . ',' . $passwordMaxLength . '}';
            } else {
                $passwordAttributes['placeholder'] = t('at least %s characters', $passwordMinLength);
                $passwordAttributes['pattern'] = '.{' . $passwordMinLength . ',}';
            }
        } elseif ($passwordMaxLength > 0) {
            $passwordAttributes['placeholder'] = t('up to %s characters', $passwordMaxLength);
            $passwordAttributes['pattern'] = '.{0,' . $passwordMaxLength . '}';
        }
        $this->set('passwordAttributes', $passwordAttributes);
        $canonicalUrl = '';
        $canonicalUrlChecked = false;
        $canonicalUrlAlternative = '';
        $canonicalUrlAlternativeChecked = false;
        $uri = $this->request->getUri();
        if (preg_match('/^(https?)(:.+?)(?:\/' . preg_quote(DISPATCHER_FILENAME, '%') . ')?\/install(?:$|\/|\?)/i', $uri, $m)) {
            switch (strtolower($m[1])) {
                case 'http':
                    $canonicalUrl = 'http' . rtrim($m[2], '/');
                    $canonicalUrlAlternative = 'https' . rtrim($m[2], '/');
                    //$canonicalUrlChecked = true;
                    break;
                case 'https':
                    $canonicalUrl = 'https' . rtrim($m[2], '/');
                    $canonicalUrlAlternative = 'http' . rtrim($m[2], '/');
                    //$canonicalUrlChecked = true;
                    break;
            }
        }
        $countries = [];
        $ll = $this->app->make('localization/languages');
        $chunks = explode('_', Localization::activeLocale());
        $computedSiteLocaleLanguage = $chunks[0];
        $languages = $ll->getLanguageList();
        $this->set('languages', $languages);
        $countries = $this->getCountriesForLanguage($computedSiteLocaleLanguage);
        $this->set('countries', $countries);
        $this->set('computedSiteLocaleLanguage', $computedSiteLocaleLanguage);
        if (isset($chunks[1])) {
            $computedSiteLocaleCountry = $chunks[1];
        } else {
            if (is_array(current($countries))) {
                $computedSiteLocaleCountry = key(current($countries));
            } else {
                $computedSiteLocaleCountry = key($countries);
            }
        }
        $this->set('computedSiteLocaleCountry', $computedSiteLocaleCountry);
        $this->set('setInitialState', $this->request->post('SITE') === null);
        $this->set('canonicalUrl', $canonicalUrl);
        $this->set('canonicalUrlChecked', $canonicalUrlChecked);
        $this->set('canonicalUrlAlternative', $canonicalUrlAlternative);
        $this->set('canonicalUrlAlternativeChecked', $canonicalUrlAlternativeChecked);
        $this->set('SERVER_TIMEZONE', @date_default_timezone_get() ?: 'UTC');
        $this->set('availableTimezones', $this->app->make('date')->getGroupedTimezones());
    }

    public function get_site_locale_countries($viewLocaleID, $languageID, $preselectedCountryID)
    {
        Localization::changeLocale($viewLocaleID);
        $countries = $this->getCountriesForLanguage($languageID);
        $form = $this->app->make('helper/form');
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->json($form->select('siteLocaleCountry', $countries, $preselectedCountryID));
    }

    private function getCountriesForLanguage($languageID)
    {
        $cl = $this->app->make('lists/countries');
        $recommendedCountries = [];
        foreach ($cl->getCountriesForLanguage($languageID) as $countryID) {
            $recommendedCountries[$countryID] = $cl->getCountryName($countryID);
        }
        $otherCountries = [];
        foreach ($cl->getCountries() as $countryID => $countryName) {
            if (!isset($recommendedCountries[$countryID])) {
                $otherCountries[$countryID] = $countryName;
            }
        }
        if (count($recommendedCountries) === 0) {
            $result = $otherCountries;
        } elseif (count($otherCountries) === 0) {
            $result = $recommendedCountries;
        } else {
            $result = [
                t('** Recommended Countries') => $recommendedCountries,
                t('** Other Countries') => $otherCountries,
            ];
        }

        return $result;
    }

    /**
     * Testing.
     */
    public function on_start()
    {
        $this->addHeaderItem('<link href="' . ASSETS_URL_CSS . '/views/install.css" rel="stylesheet" type="text/css" media="all" />');
        $this->requireAsset('core/app');
        $this->requireAsset('javascript', 'backstretch');
        $this->requireAsset('javascript', 'bootstrap/collapse');
        $locale = $this->request->request->get('locale');
        if ($locale) {
            $loc = Localization::changeLocale($locale);
            $this->set('locale', $locale);
        }
        Cache::disableAll();

        if ($this->app->isInstalled()) {
            throw new Exception(t('concrete5 is already installed.'));
        }
        $this->set('backgroundFade', 0);
        $this->set('pageTitle', t('Install concrete5'));
    }

    public function run_routine($pkgHandle, $routine)
    {
        $options = $this->getInstallerOptions();
        $options->load();
        $options->setStartingPointHandle($pkgHandle);
        $jsx = $this->app->make('helper/json');
        $js = new stdClass();
        try {
            $spl = $this->installer->getStartingPoint(false);
            $spl->executeInstallRoutine($routine);
            $js->error = false;
        } catch (Exception $e) {
            $js->error = true;
            $js->message = tc('InstallError', '%s.<br><br>Trace:<br>%s', $e->getMessage(), $e->getTraceAsString());
            $options->deleteFiles();
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($js);
    }

    /**
     * @return \Concrete\Core\Error\Error
     */
    public function configure()
    {
        $error = $this->app->make('helper/validation/error');
        /* @var $error \Concrete\Core\Error\ErrorList\ErrorList */
        try {
            $post = $this->request->request;
            $val = $this->app->make('helper/validation/form');
            /* @var \Concrete\Core\Form\Service\Validation $val */
            $val->setData($this->post());
            $val->addRequired('SITE', t("Please specify your site's name"));
            $val->addRequiredEmail('uEmail', t('Please specify a valid email address'));
            $val->addRequired('DB_DATABASE', t('You must specify a valid database name'));
            $val->addRequired('DB_SERVER', t('You must specify a valid database server'));
            $val->addRequired('SERVER_TIMEZONE', t('You must specify the system time zone'));

            $password = $post->get('uPassword');
            $passwordConfirm = $post->get('uPasswordConfirm');

            $this->app->make('validator/password')->isValid($password, $error);

            if ($password) {
                if ($password != $passwordConfirm) {
                    $error->add(t('The two passwords provided do not match.'));
                }
            }

            if (!$val->test()) {
                $error->add($val->getError());
            } elseif (!$error->has()) {
                $options = $this->app->make(InstallerOptions::class);
                /* @var InstallerOptions $options */
                $configuration = $post->get('SITE_CONFIG');
                if (!is_array($configuration)) {
                    $configuration = [];
                }
                $configuration['database'] = [
                    'default-connection' => 'concrete',
                    'connections' => [
                        'concrete' => [
                            'driver' => 'c5_pdo_mysql',
                            'server' => $post->get('DB_SERVER'),
                            'database' => $post->get('DB_DATABASE'),
                            'username' => $post->get('DB_USERNAME'),
                            'password' => $post->get('DB_PASSWORD'),
                            'charset' => 'utf8',
                        ],
                    ],
                ];
                $configuration['canonical-url'] = $post->get('canonicalUrlChecked') === '1' ? $post->get('canonicalUrl') : '';
                $configuration['canonical-url-alternative'] = $post->get('canonicalUrlAlternativeChecked') === '1' ? $post->get('canonicalUrlAlternative') : '';
                $configuration['session-handler'] = $post->get('sessionHandler');
                $options->setConfiguration($configuration);

                $config = $this->app->make('config');
                $hasher = new PasswordHash($config->get('concrete.user.password.hash_cost_log2'), $config->get('concrete.user.password.hash_portable'));
                $options
                    ->setUserEmail($post->get('uEmail'))
                    ->setUserPasswordHash($hasher->HashPassword($post->get('uPassword')))
                    ->setStartingPointHandle($post->get('SAMPLE_CONTENT'))
                    ->setSiteName($post->get('SITE'))
                    ->setSiteLocaleId($post->get('siteLocaleLanguage') . '_' . $post->get('siteLocaleCountry'))
                    ->setUiLocaleId($post->get('locale'))
                    ->setServerTimeZoneId($post->get('SERVER_TIMEZONE'))
                ;
                $installer = $this->app->make(Installer::class);
                /* @var Installer $installer */
                $installer->setOptions($options);
                $error->add($installer->checkOptions());
                if (!$error->has()) {
                    $options->save();
                    $this->redirect('/');
                }
            }
        } catch (Exception $ex) {
            $error->add($ex);
        }
        $this->getInstallerOptions()->deleteFiles();
        $this->set('error', $error);
        $this->setup();
    }

    /**
     * @return \Concrete\Core\Install\PreconditionInterface[][]
     */
    public function getPreconditions()
    {
        $service = $this->app->make(PreconditionService::class);
        /* @var PreconditionService $service */
        $required = [];
        $optional = [];
        foreach ($service->getPreconditions() as $precondition) {
            if ($precondition->isOptional()) {
                $optional[] = $precondition;
            } else {
                $required[] = $precondition;
            }
        }

        return [$required, $optional];
    }

    public function web_precondition($handle, $argument = '')
    {
        if ($prefix === '-') {
            $prefix = false;
        }
        $service = $this->app->make(PreconditionService::class);
        /* @var PreconditionService $service */
        $precondition = $service->getPreconditionByHandle($handle);
        if (!$precondition instanceof WebPreconditionInterface) {
            throw new Exception(sprintf('%s is not a valid precondition handle', $handle));
        }
        $result = $precondition->getAjaxAnswer($argument);
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->json($result);
    }
}
