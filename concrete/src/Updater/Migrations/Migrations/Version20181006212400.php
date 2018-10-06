<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Exception;

class Version20181006212400 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $params = $this->connection->getParams();
        if (!isset($params['charset']) || isset($params['character_set']) || isset($params['collation'])) {
            return;
        }
        try {
            list($charset, $collation) = $this->getPreferredCharsetCollation($this->connection);
            $this->output(t('Migrating database character set from "%1$s" to "%2$s" with collation "%3$s"...', $params['charset'], $charset, $collation));
            $this->connection->executeQuery('SET foreign_key_checks = 0');
            $sm = $this->connection->getSchemaManager();
            foreach ($sm->listTableNames() as $tableName) {
                try {
                    if ($this->updateTable($this->connection, $tableName, $charset, $collation)) {
                        $this->output(t('- converting table "%1$s": %2$s', $tableName, tc('table', 'updated.')));
                    } else {
                        $this->output(t('- converting table "%1$s": %2$s', $tableName, tc('table', 'already up-to-date.')));
                    }
                } catch (Exception $x) {
                    $this->output(t('- converting table "%1$s": %2$s', $tableName, $x->getMessage()));
                }
            }
            $config = $this->app->make('config');
            $connectionName = $config->get('database.default-connection');
            $connectionConfig = $config->get("database.connections.{$connectionName}");
            unset($connectionConfig['charset']);
            $connectionConfig['character_set'] = $charset;
            $connectionConfig['collation'] = $collation;
            $config->set("database.connections.{$connectionName}", $connectionConfig);
            $config->save("database.connections.{$connectionName}", $connectionConfig);
        } catch (Exception $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        }
        finally {
            try {
                $this->connection->executeQuery('SET foreign_key_checks = 1');
            } catch (Exception $x) {
            }
        }
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     *
     * @throws \Exception
     *
     * @return string[]
     */
    protected function getPreferredCharsetCollation(Connection $connection)
    {
        $config = $this->app->make('config');
        $charset = strtolower((string) $config->get('database.preferred_character_set'));
        if ($charset === '') {
            throw new Exception(t('no preferred character set defined.'));
        }
        $supportedCharsets = $this->connection->getSupportedCharsets();
        if (!isset($supportedCharsets[$charset])) {
            throw new Exception(t('the character set "%s" is not supported by the database.', $charset));
        }
        $collation = strtolower((string) $config->get('database.preferred_collation'));
        if ($collation === '' || $collation === $supportedCharsets[$charset]) {
            $collation = $supportedCharsets[$charset];
        } else {
            $supportedCollations = $this->connection->getSupportedCollations();
            if (!isset($supportedCollations[$collation])) {
                throw new Exception(t('the collation "%s" is not supported by the database.', $collation));
            }
            if ($supportedCollations[$collation] !== $charset) {
                throw new Exception(t('the collation "%1$s" is associated to the character set "%2%s" and not to "%3%s".', $collation, $supportedCollations[$collation], $charset));
            }
        }

        return [$charset, $collation];
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     * @param string $tableName
     * @param string $charset
     * @param string $collation
     *
     * @throws \Exception
     *
     * @return bool true: table updated, false: table already up-to-date
     */
    protected function updateTable(Connection $connection, $tableName, $charset, $collation)
    {
        $row = $connection->fetchAssoc('SHOW TABLE STATUS WHERE name like ?', [$tableName]);
        if ($row === false || !isset($row['Collation'])) {
            throw new Exception(t('failed to retrieve the table collation.'));
        }
        if (strcasecmp($collation, $row['Collation']) === 0) {
            return false;
        }
        $connection->executeQuery('ALTER TABLE ' . $connection->quoteIdentifier($tableName) . ' CONVERT TO CHARACTER SET ' . $charset . ' COLLATE ' . $collation);

        return true;
    }
}
