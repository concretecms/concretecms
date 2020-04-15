<?php

namespace Concrete\Core\Database;

use ArrayAccess;
use ArrayObject;
use Closure;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Exception;
use Throwable;

class ForeignKeyFixer
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    private $connection;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $schemaManager;

    /**
     * @var \Closure|null
     */
    private $tick;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->schemaManager = $connection->getSchemaManager();
    }

    /**
     * Set a callback to be called to log/display progress.
     *
     * @param \Closure|null $callback
     * @param null|Closure $value
     *
     * @return $this
     */
    public function setTick(Closure $value = null)
    {
        $this->tick = $value;

        return $this;
    }

    /**
     * Fix the foreign key definitions.
     *
     * @param string[]|null $tableNames the names of the database tables to be fixed (NULL means all tables)
     * @param \ArrayAccess|null $errors errors occurred during the execution will be added here
     */
    public function fixForeignKeys(array $tableNames = null, ArrayAccess $errors = null)
    {
        if ($errors === null) {
            $errors = new ArrayObject();
        }
        try {
            $this->tick(t('Listing database tables'));
            $actualTableNames = $this->schemaManager->listTableNames();
            $actualTableNamesLowerCase = array_map('strtolower', $actualTableNames);
            if ($tableNames === null) {
                $tableNames = $actualTableNames;
            }
            if ($tableNames === []) {
                $this->tick('No tables to be fixed.');

                return;
            }
            natcasesort($tableNames);
            foreach ($tableNames as $tableName) {
                $tableIndex = array_search(strtolower($tableName), $actualTableNamesLowerCase, true);
                if ($tableIndex === false) {
                    $error = new UserMessageException(t("The database table '%s' does not exist.", $tableName));
                    $errors[] = $error;
                    $this->tick($error);
                    continue;
                }
                $this->fixForeignKeysInTable($actualTableNames[$tableIndex], $actualTableNames, $errors);
            }
        } catch (Exception $x) {
            $errors[] = $x;
            $this->tick($x);
        } catch (Throwable $x) {
            $errors[] = $x;
            $this->tick($x);
        }
    }

    /**
     * @param string $tableName
     * @param string[] $actualTableNames
     * @param \ArrayAccess $errors
     */
    private function fixForeignKeysInTable($tableName, array $actualTableNames, ArrayAccess $errors)
    {
        try {
            $table = $this->schemaManager->listTableDetails($tableName);
            $foreignKeys = $table->getForeignKeys();
            if (empty($foreignKeys)) {
                return;
            }
            $this->tick(t('Checking database table %s', $tableName));
            foreach ($table->getForeignKeys() as $foreignKey) {
                $this->fixForeignKey($foreignKey, $actualTableNames, $errors);
            }
        } catch (Exception $x) {
            $errors[] = $x;
            $this->tick($x);
        } catch (Throwable $x) {
            $errors[] = $x;
            $this->tick($x);
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $foreignKey
     * @param string[] $actualTableNames
     * @param \ArrayAccess $errors
     */
    private function fixForeignKey(ForeignKeyConstraint $foreignKey, array $actualTableNames, ArrayAccess $errors)
    {
        $foreignTableName = $foreignKey->getForeignTableName();
        if (in_array($foreignTableName, $actualTableNames, true)) {
            $this->tick(t('- the foreign key \'%1$s\' (referencing table \'%2$s\') is well formed', $foreignKey->getName(), $foreignTableName));

            return;
        }
        $tableIndex = array_search(strtolower($foreignTableName), array_map('strtolower', $actualTableNames));
        if ($tableIndex === false) {
            $this->tick(t('- the foreign key \'%1$s\' references an unknown table (\'%2$s\')', $foreignKey->getName(), $foreignTableName));

            return;
        }
        $newForeignTableName = $actualTableNames[$tableIndex];
        $newForeignKey = new ForeignKeyConstraint(
            $foreignKey->getLocalColumns(),
            $newForeignTableName,
            $foreignKey->getForeignColumns(),
            $foreignKey->getName(),
            $foreignKey->getOptions()
        );
        $newForeignKey->setLocalTable($foreignKey->getLocalTable());
        try {
            $this->schemaManager->dropForeignKey($foreignKey, $foreignKey->getLocalTable());
            $this->tick(t('- the foreign key \'%1$s\' referencing \'%2$s\' has been dropped', $foreignKey->getName(), $foreignTableName));
        } catch (Exception $x) {
            $errors[] = $x;
            $this->tick($x);

            return;
        } catch (Throwable $x) {
            $errors[] = $x;
            $this->tick($x);

            return;
        }
        try {
            $this->schemaManager->createForeignKey($newForeignKey, $newForeignKey->getLocalTable());
            $this->tick(t('- the foreign key \'%1$s\' referencing \'%2$s\' has been created', $newForeignKey->getName(), $newForeignTableName));
        } catch (Exception $x) {
            $errors[] = $x;
            $this->tick($x);

            return;
        } catch (Throwable $x) {
            $errors[] = $x;
            $this->tick($x);

            return;
        }

        return true;
    }

    /**
     * @param string|\Exception|\Throwable $what
     */
    private function tick($what)
    {
        $closure = $this->tick;
        if ($closure === null) {
            return;
        }
        $closure($what);
    }
}
