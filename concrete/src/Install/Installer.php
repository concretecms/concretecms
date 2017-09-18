<?php
namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Timezone;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\Preconditions\PdoMysqlExtension;
use Concrete\Core\Package\StartingPointPackage;
use Concrete\Core\Url\UrlImmutable;
use DateTimeZone;
use Exception;
use Punic\Misc as PunicMisc;
use Throwable;

class Installer
{
    /**
     * The default starting point handle.
     *
     * @var string
     */
    const DEFAULT_STARTING_POINT = 'elemental_full';

    /**
     * The application instance.
     *
     * @var Application
     */
    protected $application;

    /**
     * The options to be used by the installer.
     *
     * @var InstallerOptions
     */
    protected $options;

    /**
     * Initialize the instance.
     *
     * @param Application $application the application instance
     * @param InstallerOptions $options the options to be used by the installer
     */
    public function __construct(Application $application, InstallerOptions $options)
    {
        $this->application = $application;
        $this->options = $options;
    }

    /**
     * Get the options to be used by the installer.
     *
     * @return InstallerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the options to be used by the installer.
     *
     * @param InstallerOptions $value
     *
     * @return $this
     */
    public function setOptions(InstallerOptions $value)
    {
        $this->options = $value;

        return $this;
    }

    /**
     * @return \Concrete\Core\Error\ErrorList\ErrorList
     */
    public function checkOptions()
    {
        $errors = $this->application->make('error');
        /* @var \Concrete\Core\Error\ErrorList\ErrorList $errors */
        try {
            $serverTimeZone = $this->options->getServerTimeZone(false);
        } catch (Exception $x) {
            $errors->add($x);
            $serverTimeZone = null;
        }
        try {
            $this->checkConnection($serverTimeZone);
        } catch (Exception $x) {
            $errors->add($x);
        }
        try {
            $this->getStartingPoint(false);
        } catch (Exception $x) {
            $errors->add($x);
        }
        try {
            $this->checkCanonicalUrls();
        } catch (Exception $x) {
            $errors->add($x);
        }

        return $errors;
    }

    /**
     * @return array|null
     */
    private function getDefaultConnectionConfiguration()
    {
        $result = null;
        $configuration = $this->getOptions()->getConfiguration();
        if (true
            && isset($configuration['database'])
            && is_array($configuration['database'])
            && isset($configuration['database']['default-connection'])
            && is_string($defaultConnection = $configuration['database']['default-connection'])
            && $defaultConnection !== ''
            && isset($configuration['database']['connections'])
            && is_array($configuration['database']['connections'])
            && isset($configuration['database']['connections'][$defaultConnection])
            && is_array($configuration['database']['connections'][$defaultConnection])
        ) {
            $result = $configuration['database']['connections'][$defaultConnection] + [
                'driver' => '',
                'server' => '',
                'database' => '',
                'username' => '',
                'password' => '',
            ];
            if (false
                || !is_string($result['driver'])
                || '' === $result['driver']
                || !is_string($result['server'])
                || '' === $result['server']
                || !is_string($result['database'])
                || '' === $result['database']
                || !is_string($result['username'])
                || '' === $result['username']
                || !is_string($result['password'])
            ) {
                $result = null;
            } else {
                $result['host'] = $result['server'];
                unset($result['server']);
            }
        }

        return $result;
    }

    /**
     * Check if the database connection is correctly configured.
     *
     * @param DateTimeZone|null $serverTimeZone The PHP time zone that should be compatible with the connection
     *
     * @throws UserMessageException
     */
    protected function checkConnection(DateTimeZone $serverTimeZone = null)
    {
        $pdoCheck = $this->application->make(PdoMysqlExtension::class)->performCheck();
        if ($pdoCheck->getState() !== PreconditionResult::STATE_PASSED) {
            throw new UserMessageException($pdoCheck->getMessage());
        }
        $databaseConfiguration = $this->getDefaultConnectionConfiguration();
        if ($databaseConfiguration === null) {
            throw new UserMessageException(t('The configuration is missing the required database connection parameters.'));
        }
        $databaseManager = $this->application->make(DatabaseManager::class);
        /* @var DatabaseManager $databaseManager */
        try {
            $db = $databaseManager->getFactory()->createConnection($databaseConfiguration);
            /* @var \Concrete\Core\Database\Connection\Connection $db */
        } catch (Exception $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode(), $x);
        } catch (Throwable $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode());
        }
        if ($this->getOptions()->isAutoAttachEnabled() === false) {
            $existingTables = [];
            foreach ($db->fetchAll('show tables') as $row) {
                $existingTables[] = array_shift($row);
            }
            $numExistingTables = count($existingTables);
            if ($numExistingTables > 0) {
                throw new UserMessageException(t2(
                    'There is already %s table in this database. concrete5 must be installed in an empty database.',
                    'There are already %s tables in this database. concrete5 must be installed in an empty database.',
                    $numExistingTables
                ));
            }
        }
        try {
            $supported = false;
            foreach ($db->fetchAll('show engines') as $engine) {
                $engine = array_change_key_case($engine, CASE_LOWER);
                if (isset($engine['engine']) && strtolower($engine['engine']) == 'innodb') {
                    $supported = true;
                    break;
                }
            }
            if (!$supported) {
                throw new UserMessageException(t('Your MySQL database does not support InnoDB database tables. These are required.'));
            }
        } catch (Exception $x) {
            // we're going to just proceed and hope for the best.
        } catch (Throwable $x) {
            // we're going to just proceed and hope for the best.
        }

        if ($serverTimeZone !== null) {
            $ctz = $this->application->make(Timezone::class, ['connection' => $db]);
            /* @var Timezone $ctz */
            $deltaTimezone = $ctz->getDeltaTimezone($serverTimeZone);
            if ($deltaTimezone !== null) {
                $error = $ctz->describeDeltaTimezone($deltaTimezone);
                $suggestTimezones = $ctz->getCompatibleTimezones();
                if (!empty($suggestTimezones)) {
                    $error .= ' ' . t('These are the time zones compatible with the database one: %s', PunicMisc::join($suggestTimezones));
                }
                throw new UserMessageException($error);
            }
        }
    }

    /**
     * Get the StartingPointPackage instance.
     *
     * @param bool $fallbackToDefault Fallback to the default one if the starting point handle is not defined?
     *
     * @throws UserMessageException
     *
     * @return StartingPointPackage
     */
    public function getStartingPoint($fallbackToDefault)
    {
        $handle = $this->getOptions()->getStartingPointHandle();
        if ($handle === '') {
            if (!$fallbackToDefault) {
                throw new UserMessageException(t('The starting point has not been defined.'));
            }
            $handle = static::DEFAULT_STARTING_POINT;
        }
        $result = StartingPointPackage::getClass($handle);
        if ($result === null) {
            throw new UserMessageException(t('Invalid starting point: %s', $handle));
        }
        $result->setInstallerOptions($this->getOptions());

        return $result;
    }

    /**
     * Check that the canonical URLs are well formatted.
     *
     * @throws UserMessageException
     */
    protected function checkCanonicalUrls()
    {
        $configuration = $this->getOptions()->getConfiguration();
        foreach ([
            'canonical-url' => t('The canonical URL must have the http:// scheme or the https:// scheme'),
            'canonical-url-alternative' => t('The alternative canonical URL must have the http:// scheme or the https:// scheme'),
        ] as $handle => $error) {
            if (isset($configuration[$handle]) && $configuration[$handle] !== '') {
                $url = UrlImmutable::createFromUrl($configuration[$handle]);
                if (!preg_match('/^https?/i', $url->getScheme())) {
                    throw new UserMessageException($error);
                }
                $configuration[$handle] = (string) $url;
            }
        }
    }
}
