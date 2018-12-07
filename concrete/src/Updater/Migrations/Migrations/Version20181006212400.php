<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Exception;
use Throwable;

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
        $resolver = $this->app->make(Resolver::class);
        try {
            list($charset, $collation) = $resolver->resolveCharacterSetAndCollation($this->connection);
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
            $config->set("database.connections.{$connectionName}.character_set", $charset);
            $config->save("database.connections.{$connectionName}.character_set", $charset);
            $config->set("database.connections.{$connectionName}.collation", $collation);
            $config->save("database.connections.{$connectionName}.collation", $collation);
        } catch (Exception $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        } catch (Throwable $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        } finally {
            try {
                $this->connection->executeQuery('SET foreign_key_checks = 1');
            } catch (Exception $x) {
            }
        }
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
