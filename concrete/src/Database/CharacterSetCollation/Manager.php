<?php

namespace Concrete\Core\Database\CharacterSetCollation;

use Concrete\Core\Config\DirectFileSaver;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Exception as BaseException;
use Illuminate\Filesystem\Filesystem;
use Throwable;

class Manager
{
    /**
     * @var \Concrete\Core\Database\DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var \Concrete\Core\Database\CharacterSetCollation\Resolver
     */
    protected $resolver;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param \Concrete\Core\Database\CharacterSetCollation\Resolver $resolver
     * @param DatabaseManager $databaseManager
     * @param Filesystem $fileSystem
     */
    public function __construct(DatabaseManager $databaseManager, Resolver $resolver, Filesystem $fileSystem)
    {
        $this->databaseManager = $databaseManager;
        $this->resolver = $resolver;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Apply the character set and collation to a connection.
     *
     * @param string $characterSet the character set to be applied (if empty, we'll derive it from the collation)
     * @param string $collation the collation to be applied (if empty, we'll use the character set default one)
     * @param string $connectionName the name of the connection (if empty, we'll use the default connection)
     * @param string $environment
     * @param callable|null $messageCallback a callback function that will receive progress messages
     * @param \Concrete\Core\Error\ErrorList\ErrorList|null $warnings if specified, conversion errors will be added to this ErrorList
     *
     * @throws \Exception
     */
    public function apply($characterSet, $collation, $connectionName = '', $environment = '', callable $messageCallback = null, ErrorList $warnings = null)
    {
        if ($messageCallback === null) {
            $messageCallback = function ($message) { };
        }
        if ((string) $connectionName === '') {
            $connectionName = $this->databaseManager->getDefaultConnection();
        }
        $connection = $this->databaseManager->connection($connectionName);
        list($characterSet, $collation) = $this->resolver
            ->setCharacterSet((string) $characterSet)
            ->setCollation((string) $collation)
            ->resolveCharacterSetAndCollation($connection)
        ;
        $messageCallback(t('Setting character set "%1$s" and collation "%2$s" for connection "%3$s"', $characterSet, $collation, $connectionName));
        $this->convertTables($connection, $characterSet, $collation, $messageCallback, $warnings);
        $messageCallback(t('Saving connection configuration.'));
        $this->persistConfiguration($connectionName, $environment, $characterSet, $collation, $warnings);
        $connection->refreshCharactersetCollation($characterSet, $collation);
    }

    /**
     * Re-apply the configured character set and collation to all the database tables.
     */
    public function reapply(Connection $connection, callable $messageCallback = null, ErrorList $warnings = null)
    {
        if ($messageCallback === null) {
            $messageCallback = static function ($message) { };
        }
        $params = $connection->getParams();
        $characterSet = (string) array_get($params, 'character_set');
        $collation = (string) array_get($params, 'collation');
        if ($characterSet === '' && $collation === '') {
            $characterSet = (string) array_get($params, 'charset');
        }
        list($characterSet, $collation) = $this->resolver
            ->setCharacterSet($characterSet)
            ->setCollation($collation)
            ->resolveCharacterSetAndCollation($connection)
        ;
        $messageCallback(t('Setting character set "%1$s" and collation "%2$s"', $characterSet, $collation));
        $this->convertTables($connection, $characterSet, $collation, $messageCallback, $warnings);
    }

    /**
     * Convert all the database tables a specific character set/collation combination.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param callable|null $messageCallback a callback function that will receive progress messages
     * @param string $characterSet
     * @param string $collation
     * @param \Concrete\Core\Error\ErrorList\ErrorList $warnings
     *
     * @throws \Exception
     */
    protected function convertTables(Connection $connection, $characterSet, $collation, callable $messageCallback, ErrorList $warnings = null)
    {
        $schemaManager = $connection->getSchemaManager();
        $tableNames = $schemaManager->listTableNames();
        $connection->executeQuery('SET foreign_key_checks = 0');
        try {
            foreach ($tableNames as $tableName) {
                $updated = false;
                $error = null;
                try {
                    $updated = $this->convertTable($connection, $tableName, $characterSet, $collation);
                } catch (BaseException $x) {
                    $error = $x;
                } catch (Throwable $x) {
                    $error = $x;
                }
                if ($error !== null) {
                    $messageCallback(t('- converting table "%1$s": %2$s', $tableName, $error->getMessage()));
                    if ($warnings !== null) {
                        $warnings->add($error);
                    }
                } elseif ($updated) {
                    $messageCallback(t('- converting table "%1$s": %2$s', $tableName, tc('table', 'updated.')));
                } else {
                    $messageCallback(t('- converting table "%1$s": %2$s', $tableName, tc('table', 'already up-to-date.')));
                }
            }
        } finally {
            $connection->executeQuery('SET foreign_key_checks = 1');
        }
    }

    /**
     * Convert a table to a specific character set/collation combination.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param string $tableName
     * @param string $characterSet
     * @param string $collation
     *
     * @throws \Exception
     *
     * @return bool true: table updated, false: table already up-to-date
     */
    protected function convertTable(Connection $connection, $tableName, $characterSet, $collation)
    {
        $row = $connection->fetchAssoc('SHOW TABLE STATUS WHERE name like ?', [$tableName]);
        if ($row === false || !isset($row['Collation'])) {
            throw new UserMessageException(t('Failed to retrieve the table collation.'));
        }
        if (strcasecmp($collation, $row['Collation']) === 0) {
            return false;
        }
        $connection->executeQuery('ALTER TABLE ' . $connection->quoteIdentifier($tableName) . ' CONVERT TO CHARACTER SET ' . $characterSet . ' COLLATE ' . $collation);

        return true;
    }

    /**
     * Persist the character set/collation configuration for a specific connection.
     *
     * @param string $connectionName
     * @param string $environment
     * @param string $characterSet
     * @param string $collation
     */
    protected function persistConfiguration($connectionName, $environment, $characterSet, $collation)
    {
        $key = "database.connections.{$connectionName}";
        $config = $this->resolver->getConfig();
        $data = $config->get($key);
        if (isset($data['charset'])) {
            unset($data['charset']);
            $config->set("{$key}.charset", null);
        }
        $data['character_set'] = $characterSet;
        $config->set("{$key}.character_set", $characterSet);
        $data['collation'] = $collation;
        $config->set("{$key}.collation", $collation);
        $config2 = $this->getRepository($environment);
        $config2->save($key, $data);
    }

    /**
     * @param string $environment
     *
     * @return \Concrete\Core\Config\Repository\Repository
     */
    private function getRepository($environment)
    {
        $environment = (string) $environment;
        $config = $this->resolver->getConfig();
        $defaultEnvironment = (string) $config->getEnvironment();
        if ($environment === '') {
            $environment = $defaultEnvironment;
        }

        $file_loader = new FileLoader($this->fileSystem);
        $file_saver = new DirectFileSaver($this->fileSystem, $environment === $defaultEnvironment ? null : $environment);

        return new Repository($file_loader, $file_saver, $environment);
    }
}
