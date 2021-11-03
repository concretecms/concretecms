<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\CharacterSetCollation\Exception as CharacterSetCollationException;
use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Database\Connection\Connection;
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
    const DEFAULT_STARTING_POINT = 'atomik_full';

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
     * @var \Concrete\Core\Database\CharacterSetCollation\Resolver
     */
    protected $characterSetCollationResolver;

    /**
     * Initialize the instance.
     *
     * @param Application $application the application instance
     * @param InstallerOptions $options the options to be used by the installer
     * @param \Concrete\Core\Database\CharacterSetCollation\Resolver $characterSetCollationResolver
     */
    public function __construct(Application $application, InstallerOptions $options, Resolver $characterSetCollationResolver)
    {
        $this->application = $application;
        $this->options = $options;
        $this->characterSetCollationResolver = $characterSetCollationResolver;
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
            $connection = $databaseManager->getFactory()->createConnection($databaseConfiguration);
        } catch (Exception $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode(), $x);
        } catch (Throwable $x) {
            throw new UserMessageException($x->getMessage(), $x->getCode());
        }

        $connection = $this->setPreferredCharsetCollation($connection);

        return $connection;
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

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     *
     * @return \Concrete\Core\Database\Connection\Connection
     */
    private function setPreferredCharsetCollation(Connection $connection)
    {
        // Let's get the currently configured connection charset and collation
        $connectionParams = $connection->getParams();
        $connectionCharset = isset($connectionParams['character_set']) ? $this->characterSetCollationResolver->normalizeCharacterSet($connectionParams['character_set']) : '';
        $connectionCollation = isset($connectionParams['collation']) ? $this->characterSetCollationResolver->normalizeCollation($connectionParams['collation']) : '';
        try {
            list($characterSet, $collation) = $this->characterSetCollationResolver->resolveCharacterSetAndCollation($connection);
        } catch (CharacterSetCollationException $x) {
            // Unsupported character set and/or collation
            return $connection;
        }
        if ($connectionCharset === $characterSet && $connectionCollation === $collation) {
            // No changes required
            return $connection;
        }

        return $this->reconfigureCharacterSetCollation($connection, $characterSet, $collation);
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param string $characterSet
     * @param string $collation
     *
     * @return \Concrete\Core\Database\Connection\Connection
     */
    private function reconfigureCharacterSetCollation(Connection $connection, $characterSet, $collation)
    {
        $configuration = $this->getOptions()->getConfiguration();
        $defaultConnectionName = isset($configuration['database']['default-connection']) ? $configuration['database']['default-connection'] : '';
        if (!$defaultConnectionName) {
            // We should always have a default connection name, but don't break the process if it's not so
            return $connection;
        }
        $defaultConnectionConfiguration = isset($configuration['database']['connections'][$defaultConnectionName]) ? $configuration['database']['connections'][$defaultConnectionName] : null;
        if (!is_array($defaultConnectionConfiguration)) {
            // We should always have the configuration of the default connection name, but don't break the process if it's not so
            return $connection;
        }
        $configuration['database']['connections'][$defaultConnectionName]['character_set'] = $characterSet;
        $configuration['database']['connections'][$defaultConnectionName]['collation'] = $collation;
        $this->getOptions()->setConfiguration($configuration);
        $connection->close();

        return $this->createConnection();
    }
}
