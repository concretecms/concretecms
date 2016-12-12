<?php
namespace Concrete\Controller;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Config\Renderer;
use Concrete\Core\Error\Error;
use Concrete\Core\Localization\Localization as Localization;
use Controller;
use Config;
use Exception;
use Hautelook\Phpass\PasswordHash;
use Core;
use StartingPointPackage;
use View;

defined('C5_EXECUTE') or die("Access Denied.");

if (!ini_get('safe_mode')) {
    @set_time_limit(1000);
}

class Install extends Controller
{
    /**
     * This is to check if comments are being stripped
     * Doctrine ORM depends on comments not being stripped.
     *
     * @var int
     */
    protected $docCommentCanary = 1;

    /**
     * If the database already exists and is valid, lets just attach to it rather than installing over it.
     *
     * @var bool
     */
    protected $auto_attach = false;

    protected $fp;
    protected $fpu;

    public $helpers = array('form', 'html');

    public function getViewObject()
    {
        $v = new View('/frontend/install');
        $v->setViewTheme('concrete');

        return $v;
    }

    public function view()
    {
        $locales = $this->getLocales();
        $this->set('locales', $locales);
        $this->testAndRunInstall();
    }

    protected function getLocales()
    {
        return Localization::getAvailableInterfaceLanguageDescriptions();
    }

    protected function testAndRunInstall()
    {
        if (file_exists(DIR_CONFIG_SITE . '/site_install_user.php')) {
            require DIR_CONFIG_SITE . '/site_install.php';
            @include DIR_CONFIG_SITE . '/site_install_user.php';
            if (defined('SITE_INSTALL_LOCALE') && Localization::activeLocale() !== SITE_INSTALL_LOCALE) {
                Localization::changeLocale(SITE_INSTALL_LOCALE);
            }
            $e = Core::make('helper/validation/error');
            $e = $this->validateDatabase($e);
            if (defined('INSTALL_STARTING_POINT') && INSTALL_STARTING_POINT) {
                $spName = INSTALL_STARTING_POINT;
            } else {
                $spName = 'elemental_full';
            }
            $spl = StartingPointPackage::getClass($spName);
            if ($spl === null) {
                $e->add(t('Invalid starting point: %s', $spName));
            }
            if ($e->has()) {
                $this->set('error', $e);
            } else {
                $this->set('installPackage', $spl->getPackageHandle());
                $this->set('installRoutines', $spl->getInstallRoutines());
                $this->set(
                    'successMessage',
                    t(
                        'Congratulations. concrete5 has been installed. You have been logged in as <b>%s</b> with the password you chose. If you wish to change this password, you may do so from the users area of the dashboard.',
                        USER_SUPER
                    )
                );
            }
        }
    }

    protected function validateDatabase(Error $e)
    {
        if (!extension_loaded('pdo')) {
            $e->add($this->getDBErrorMsg());
        } else {
            $DB_SERVER = isset($_POST['DB_SERVER']) ? $_POST['DB_SERVER'] : null;
            $DB_DATABASE = isset($_POST['DB_DATABASE']) ? $_POST['DB_DATABASE'] : null;
            $db = \Database::getFactory()->createConnection(
                array(
                    'host' => $DB_SERVER,
                    'user' => isset($_POST['DB_USERNAME']) ? $_POST['DB_USERNAME'] : null,
                    'password' => isset($_POST['DB_PASSWORD']) ? $_POST['DB_PASSWORD'] : null,
                    'database' => $DB_DATABASE,
                )
            );

            if ($DB_SERVER && $DB_DATABASE) {
                if (!$db) {
                    $e->add(t('Unable to connect to database.'));
                } elseif (!$this->isAutoAttachEnabled()) {
                    $num = $db->GetCol("show tables");
                    if (count($num) > 0) {
                        $e->add(
                            t(
                                'There are already %s tables in this database. concrete5 must be installed in an empty database.',
                                count($num)
                            )
                        );
                    }

                    try {
                        $support = $db->GetAll('show engines');
                        $supported = false;
                        foreach ($support as $engine) {
                            $engine = array_change_key_case($engine, CASE_LOWER);
                            if (isset($engine['engine']) && strtolower($engine['engine']) == 'innodb') {
                                $supported = true;
                            }
                        }
                        if (!$supported) {
                            $e->add(t('Your MySQL database does not support InnoDB database tables. These are required.'));
                        }
                    } catch (\Exception $exception) {
                        // we're going to just proceed and hope for the best.
                    }
                }
            }
        }

        return $e;
    }

    public function getDBErrorMsg()
    {
        return t('The PDO extension is not loaded.');
    }

    public function setup()
    {
    }

    public function select_language()
    {
    }

    /**
     * Testing.
     */
    public function on_start()
    {
        if (isset($_POST['locale']) && $_POST['locale']) {
            $loc = Localization::changeLocale($_POST['locale']);
            $this->set('locale', $_POST['locale']);
        }
        Cache::disableAll();
        $this->setRequiredItems();
        $this->setOptionalItems();

        if (\Core::isInstalled()) {
            throw new Exception(t('concrete5 is already installed.'));
        }
        if (!isset($_COOKIE['CONCRETE5_INSTALL_TEST'])) {
            setcookie('CONCRETE5_INSTALL_TEST', '1', 0, DIR_REL . '/');
        }

        $this->set('pageTitle', t('Install concrete5'));
    }

    private function setRequiredItems()
    {
        //        $this->set('imageTest', function_exists('imagecreatetruecolor') || class_exists('Imagick'));
        $this->set('imageTest', function_exists('imagecreatetruecolor')
            && function_exists('imagepng')
            && function_exists('imagegif')
            && function_exists('imagejpeg'));
        $this->set('mysqlTest', extension_loaded('pdo_mysql'));
        $this->set('i18nTest', function_exists('ctype_lower'));
        $this->set('domTest', extension_loaded('dom'));
        $this->set('jsonTest', extension_loaded('json'));
        $this->set('xmlTest', function_exists('xml_parse') && function_exists('simplexml_load_file'));
        $this->set('fileWriteTest', $this->testFileWritePermissions());
        $this->set('aspTagsTest', ini_get('asp_tags') == false);
        $rf = new \ReflectionObject($this);
        $rp = $rf->getProperty('docCommentCanary');
        $this->set('docCommentTest', (bool) $rp->getDocComment());

        $memoryLimit = ini_get('memory_limit');
        if ($memoryLimit == -1) {
            $this->set('memoryTest', 1);
            $this->set('memoryBytes', 0);
        } else {
            $val = Core::make('helper/number')->getBytes($memoryLimit);
            $this->set('memoryBytes', $val);
            if ($val < 25165824) {
                $this->set('memoryTest', -1);
            } elseif ($val >= 67108864) {
                $this->set('memoryTest', 1);
            } else {
                $this->set('memoryTest', 0);
            }
        }

        $phpVmin = $this->getMinimumPhpVersion();
        if (version_compare(PHP_VERSION, $phpVmin, '>=')) {
            $phpVtest = true;
        } else {
            $phpVtest = false;
        }
        $this->set('phpVmin', $phpVmin);
        $this->set('phpVtest', $phpVtest);
    }

    private function testFileWritePermissions()
    {
        $e = Core::make('helper/validation/error');
        if (!is_writable(DIR_CONFIG_SITE)) {
            $e->add(t('Your configuration directory config/ does not appear to be writable by the web server.'));
        }

        if (!is_writable(DIR_FILES_UPLOADED_STANDARD)) {
            $e->add(t('Your files directory files/ does not appear to be writable by the web server.'));
        }

        if (!is_writable(DIR_PACKAGES)) {
            $e->add(t('Your packages directory packages/ does not appear to be writable by the web server.'));
        }

        $this->fileWriteErrors = $e;
        if ($this->fileWriteErrors->has()) {
            return false;
        } else {
            return true;
        }
    }

    private function setOptionalItems()
    {
        // no longer need lucene
        //$this->set('searchTest', function_exists('iconv') && function_exists('mb_strtolower') && (@preg_match('/\pL/u', 'a') == 1));
        $this->set('remoteFileUploadTest', function_exists('iconv'));
        $this->set('fileZipTest', class_exists('ZipArchive'));
    }

    public function passedRequiredItems()
    {
        if ($this->get('imageTest') && $this->get('mysqlTest') && $this->get('fileWriteTest') &&
            $this->get('xmlTest') && $this->get('phpVtest') && $this->get('i18nTest') &&
            $this->get('memoryTest') !== -1 && $this->get('docCommentTest') && $this->get('aspTagsTest') &&
            $this->get('domTest')
        ) {
            return true;
        }
    }

    public function test_url($num1, $num2)
    {
        $js = Core::make('helper/json');
        $num = $num1 + $num2;
        echo $js->encode(array('response' => $num));
        exit;
    }

    public function run_routine($pkgHandle, $routine)
    {
        $spl = StartingPointPackage::getClass($pkgHandle);
        require DIR_CONFIG_SITE . '/site_install.php';
        @include DIR_CONFIG_SITE . '/site_install_user.php';

        $jsx = Core::make('helper/json');
        $js = new \stdClass();

        try {
            if ($spl === null) {
                throw new Exception(t('Invalid starting point: %s', $pkgHandle));
            }
            call_user_func(array($spl, $routine));
            $js->error = false;
        } catch (Exception $e) {
            $js->error = true;
            $js->message = tc('InstallError', '%s.<br><br>Trace:<br>%s', $e->getMessage(), $e->getTraceAsString());
            $this->reset();
        }
        echo $jsx->encode($js);
        exit;
    }

    public function reset()
    {
        // remove site.php so that we can try again ?

        if (is_resource($this->fp)) {
            fclose($this->fp);
        }
        if (file_exists(DIR_CONFIG_SITE . '/site_install.php')) {
            unlink(DIR_CONFIG_SITE . '/site_install.php');
        }
        if (file_exists(DIR_CONFIG_SITE . '/site_install_user.php')) {
            unlink(DIR_CONFIG_SITE . '/site_install_user.php');
        }
    }

    /**
     * @return \Concrete\Core\Error\Error
     */
    public function configure()
    {
        $error = \Core::make('helper/validation/error');
        /* @var $error \Concrete\Core\Error\Error */
        try {
            $val = Core::make('helper/validation/form');
            $val->setData($this->post());
            $val->addRequired("SITE", t("Please specify your site's name"));
            $val->addRequiredEmail("uEmail", t('Please specify a valid email address'));
            $val->addRequired("DB_DATABASE", t('You must specify a valid database name'));
            $val->addRequired("DB_SERVER", t('You must specify a valid database server'));

            $password = $_POST['uPassword'];
            $passwordConfirm = $_POST['uPasswordConfirm'];

            Core::make('validator/password')->isValid($password, $error);

            if ($password) {
                if ($password != $passwordConfirm) {
                    $error->add(t('The two passwords provided do not match.'));
                }
            }

            if (is_object($this->fileWriteErrors)) {
                foreach ($this->fileWriteErrors->getList() as $msg) {
                    $error->add($msg);
                }
            }

            $error = $this->validateDatabase($error);
            $error = $this->validateSampleContent($error);

            if ($val->test() && (!$error->has())) {

                // write the config file
                $vh = Core::make('helper/validation/identifier');
                $this->fp = @fopen(DIR_CONFIG_SITE . '/site_install.php', 'w+');
                $this->fpu = @fopen(DIR_CONFIG_SITE . '/site_install_user.php', 'w+');
                if ($this->fp) {
                    $config = isset($_POST['SITE_CONFIG']) ? ((array) $_POST['SITE_CONFIG']) : array();
                    $config['database'] = array(
                        'default-connection' => 'concrete',
                        'connections' => array(
                            'concrete' => array(
                                'driver' => 'c5_pdo_mysql',
                                'server' => $_POST['DB_SERVER'],
                                'database' => $_POST['DB_DATABASE'],
                                'username' => $_POST['DB_USERNAME'],
                                'password' => $_POST['DB_PASSWORD'],
                                'charset' => 'utf8',
                            ),
                        ),
                    );

                    $renderer = new Renderer($config);
                    fwrite($this->fp, $renderer->render());

                    fclose($this->fp);
                    chmod(DIR_CONFIG_SITE . '/site_install.php', 0700);
                } else {
                    throw new Exception(t('Unable to open config/app.php for writing.'));
                }

                if ($this->fpu) {
                    $hasher = new PasswordHash(Config::get('concrete.user.password.hash_cost_log2'), Config::get('concrete.user.password.hash_portable'));
                    $configuration = "<?php\n";
                    $configuration .= "define('INSTALL_USER_EMAIL', " . var_export((string) $_POST['uEmail'], true) . ");\n";
                    $configuration .= "define('INSTALL_USER_PASSWORD_HASH', " . var_export((string) $hasher->HashPassword($_POST['uPassword']), true) . ");\n";
                    $configuration .= "define('INSTALL_STARTING_POINT', " . var_export((string) $this->post('SAMPLE_CONTENT'), true) . ");\n";
                    $configuration .= "define('SITE', " . var_export((string) $_POST['SITE'], true) . ");\n";
                    if (Localization::activeLocale() != '' && Localization::activeLocale() != 'en_US') {
                        $configuration .= "define('SITE_INSTALL_LOCALE', " . var_export((string) Localization::activeLocale(), true) . ");\n";
                    }
                    $res = fwrite($this->fpu, $configuration);
                    fclose($this->fpu);
                    chmod(DIR_CONFIG_SITE . '/site_install_user.php', 0700);
                    if (PHP_SAPI != 'cli') {
                        $this->redirect('/');
                    }
                } else {
                    throw new Exception(t('Unable to open config/site_user.php for writing.'));
                }
            } else {
                if ($error->has()) {
                    $this->set('error', $error);
                } else {
                    $error = $val->getError();
                    $this->set('error', $val->getError());
                }
            }
        } catch (Exception $ex) {
            $this->reset();
            $this->set('error', $ex);
            $error->add($ex);
        }

        return $error;
    }

    protected function validateSampleContent($e)
    {
        $pkg = StartingPointPackage::getClass($this->post('SAMPLE_CONTENT'));

        if ($pkg === null) {
            $e->add(t("You must select a valid sample content starting point."));
        }

        return $e;
    }

    public function getMinimumPhpVersion()
    {
        return '5.3.3';
    }

    /**
     * @return bool
     */
    public function isAutoAttachEnabled()
    {
        return $this->auto_attach;
    }

    /**
     * @param bool $auto_attach
     */
    public function setAutoAttach($auto_attach)
    {
        $this->auto_attach = $auto_attach;
    }
}
