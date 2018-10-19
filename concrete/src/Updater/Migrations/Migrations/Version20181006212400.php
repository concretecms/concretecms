<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Exception;

class Version20181006212400 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    protected $schemaManager;

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
            try {
                $this->convertDatabase($charset, $collation);
            } finally {
                try {
                    $this->connection->executeQuery('SET foreign_key_checks = 1');
                } catch (Exception $foo) {
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
            $this->output(t('- failed to set character sets: %s', $x->getMessage()));
        } finally {
            try {
                $this->connection->executeQuery('SET foreign_key_checks = 1');
            } catch (Exception $x) {
            }
        }
    }

    /**
     * Convert the whole database to the specified charset/collation.
     *
     * @param string $charset
     * @param string $collation
     */
    protected function convertDatabase($charset, $collation)
    {
        $sm = $this->connection->getSchemaManager();
        $this->output(t('- analyzing database...'));
        $tables = [];
        foreach ($sm->listTables() as $table) {
            $tables[strtolower($table->getName())] = $table;
        }
        $allFieldsToSkip = [];
        foreach ($tables as $table) {
            $allFieldsToSkip = array_merge_recursive(
                $allFieldsToSkip,
                $this->getTableLongIndexes($table, $sm, $tables),
                $this->getTableLongForeignKeys($table, $sm, $tables)
            );
        }
        $allFieldsToSkip = array_merge_recursive(
            $allFieldsToSkip,
            $this->getManualLongIndexes($sm, $tables)
        );
        foreach ($tables as $lowerCaseTableName => $table) {
            $fieldsToSkip = isset($allFieldsToSkip[$lowerCaseTableName]) ? array_keys($allFieldsToSkip[$lowerCaseTableName]) : [];
            try {
                if ($this->updateTable($table, $fieldsToSkip, $charset, $collation)) {
                    $this->output(t('- converting table "%1$s": %2$s', $table->getName(), tc('table', 'updated.')));
                } else {
                    $this->output(t('- converting table "%1$s": %2$s', $table->getName(), tc('table', 'already up-to-date.')));
                }
            } catch (Exception $x) {
                $this->output(t('- converting table "%1$s": %2$s', $table->getName(), $x->getMessage()));
            }
        }
    }

    /**
     * Check if a column is a long text field that may not support utf8mb4.
     *
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param int|null $length
     *
     * @return bool
     */
    private function isLongTextColumn(Column $column, $length = null)
    {
        if ($length === null) {
            $length = (int) $column->getLength();
        }
        $type = $column->getType();
        if ($type instanceof StringType || $type instanceof TextType) {
            if ($length > 191) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a field not to be convert to the list of fields not to be converted.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param array $result
     */
    private function addFieldToSkipFieldsResult(Table $table, Column $column, array &$result)
    {
        $lowerCaseTableName = strtolower($table->getName());
        if (!isset($result[$lowerCaseTableName])) {
            $result[$lowerCaseTableName] = [];
        }
        $result[$lowerCaseTableName][strtolower($column->getName())] = true;
    }

    /**
     * Collect the fields not to be converted from the table indexes.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $sm
     * @param \Doctrine\DBAL\Schema\Table[] $tables
     *
     * @return array[]
     */
    private function getTableLongIndexes(Table $table, AbstractSchemaManager $sm, array $tables)
    {
        $result = [];
        foreach ($table->getIndexes() as $index) {
            if (!$index->hasFlag('fulltext') && !$index->hasFlag('spatial')) {
                foreach ($index->getColumns() as $columnName) {
                    $column = $table->getColumn($columnName);
                    if ($this->isLongTextColumn($column)) {
                        $this->addFieldToSkipFieldsResult($table, $column, $result);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Collect the fields not to be converted from the foreign keys.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $sm
     * @param \Doctrine\DBAL\Schema\Table[] $tables
     *
     * @return array[]
     */
    private function getTableLongForeignKeys(Table $table, AbstractSchemaManager $sm, array $tables)
    {
        $result = [];
        foreach ($table->getForeignKeys() as $foreignKey) {
            $foreignColumnNames = $foreignKey->getForeignColumns();
            $foreignTable = null;
            foreach ($foreignKey->getColumns() as $columnIndex => $columnName) {
                $column = $table->getColumn($columnName);
                if ($this->isLongTextColumn($column)) {
                    if ($foreignTable == null) {
                        $foreignTable = $tables[strtolower($foreignKey->getForeignTableName())];
                    }
                    $foreignColumn = $foreignTable->getColumn($foreignColumnNames[$columnIndex]);
                    $this->addFieldToSkipFieldsResult($table, $column, $result);
                    $this->addFieldToSkipFieldsResult($foreignTable, $foreignColumn, $result);
                }
            }
        }

        return $result;
    }

    /**
     * Collect the fields not to be converted from the manual index definitions.
     *
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $sm
     * @param \Doctrine\DBAL\Schema\Table[] $tables
     *
     * @return array[]
     */
    private function getManualLongIndexes(AbstractSchemaManager $sm, array $tables)
    {
        $result = [];
        $config = $this->app->make('config');
        $textIndexes = $config->get('database.text_indexes');
        if (is_array($textIndexes)) {
            foreach ($textIndexes as $tableName => $indexes) {
                $lowerCaseTableName = strtolower($tableName);
                if (isset($tables[$lowerCaseTableName])) {
                    $table = $tables[$lowerCaseTableName];
                    foreach ($indexes as $columnDefinitions) {
                        foreach ((array) $columnDefinitions as $columnDefinition) {
                            $columnDefinition = (array) $columnDefinition;
                            $columnName = array_shift($columnDefinition);
                            $indexLength = array_pop($columnDefinition);
                            if ($table->hasColumn($columnName)) {
                                $column = $table->getColumn($columnName);
                                if ($this->isLongTextColumn($column, $indexLength)) {
                                    $this->addFieldToSkipFieldsResult($table, $column, $result);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Update charset/collation of a table.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param string[] $fieldsToSkip
     * @param string $charset
     * @param string $collation
     *
     * @throws \Exception
     *
     * @return bool true: table updated; false: table already up-to-date
     */
    private function updateTable(Table $table, array $fieldsToSkip, $charset, $collation)
    {
        if ($this->isTableAlreadyConverted($table, $charset, $collation)) {
            return false;
        }
        if (count($fieldsToSkip) === 0) {
            $this->setTableCharset($table, $charset, $collation, true);
        } else {
            foreach ($table->getColumns() as $column) {
                if (!in_array(strtolower($column->getName()), $fieldsToSkip)) {
                    if ($this->isStringColumn($column)) {
                        $this->convertColumn($table, $column, $charset, $collation);
                    }
                }
            }
            $this->setTableCharset($table, $charset, $collation, false);
        }

        return true;
    }

    /**
     * Check if a table already has the specified charset/collation as the default ones.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param string $charset
     * @param string $collation
     *
     * @throws \Exception
     *
     * @return bool true
     */
    private function isTableAlreadyConverted(Table $table, $charset, $collation)
    {
        $row = $this->connection->fetchAssoc('SHOW TABLE STATUS WHERE name like ?', [$table->getName()]);
        if ($row === false || !isset($row['Collation'])) {
            throw new Exception(t('failed to retrieve the table collation.'));
        }

        return strcasecmp($collation, $row['Collation']) === 0;
    }

    /**
     * Set charset/collation for a table.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param string $charset
     * @param string $collation
     * param bool $convertColumns
     * @param mixed $convertColumns
     */
    private function setTableCharset(Table $table, $charset, $collation, $convertColumns)
    {
        if ($convertColumns) {
            $this->connection->executeQuery('ALTER TABLE ' . $this->connection->quoteIdentifier($table->getName()) . ' CONVERT TO CHARACTER SET ' . $charset . ' COLLATE ' . $collation);
        } else {
            $this->connection->executeQuery('ALTER TABLE ' . $this->connection->quoteIdentifier($table->getName()) . ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation);
        }
    }

    /**
     * Is a column a TEXT/STRING column?
     *
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return bool
     */
    private function isStringColumn(Column $column)
    {
        $type = $column->getType();
        if ($type instanceof StringType || $type instanceof TextType) {
            return true;
        }

        return false;
    }

    /**
     * Convert a specific table column to the specified charset/collation.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param string $charset
     * @param string $collation
     */
    private function convertColumn(Table $table, Column $column, $charset, $collation)
    {
        $tableDiff = new TableDiff($table->getName());
        $newColumn = clone $column;
        $newColumn->setPlatformOption('collation', $collation);
        $tableDiff->changedColumns[] = new ColumnDiff($column->getName(), $newColumn);
        $this->connection->getSchemaManager()->alterTable($tableDiff);
    }
}
