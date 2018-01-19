<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Install\Preconditions\PdoMysqlExtension;
use Concrete\Core\Package\StartingPointPackage;
use Exception;
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
     * Create a new Connection instance using the values specified in the options.
     *
     * @throws \Concrete\Core\Error\UserMessageException throws a UserMessageException in case of problems
     *
     * @return \Concrete\Core\Database\Connection\Connection
     */
    public function createConnection()
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
        try {
            return $databaseManager->getFactory()->createConnection($databaseConfiguration);
        } catch (Exception $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode(), $x);
        } catch (Throwable $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode());
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
     * @deprecated Use the OptionsPreconditionInterface preconditions
     * @see \Concrete\Core\Install\PreconditionService::getOptionsPreconditions()
     */
    public function checkOptions()
    {
        return $this->application->make('error');
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
}
