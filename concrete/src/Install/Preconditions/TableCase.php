<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class TableCase implements ConnectionOptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * @var \Concrete\Core\Database\Connection\Connection|null
     */
    protected $connection;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Table case');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'table_case';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\OptionsPreconditionInterface::setInstallerOptions()
     */
    public function setInstallerOptions(InstallerOptions $installerOptions)
    {
        $this->installerOptions = $installerOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\ConnectionOptionsPreconditionInterface::setConnection()
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        try {
            $lctn = $this->connection->fetchColumn('SELECT @@lower_case_table_names');
            if (!is_numeric($lctn)) {
                $result = new PreconditionResult(PreconditionResult::STATE_WARNING, t('Failed to get the value of the %s MySQL variable', 'lower_case_table_names'));
            } else {
                $lctn = (int) $lctn;
                // https://dev.mysql.com/doc/refman/5.7/en/identifier-case-sensitivity.html
                switch ($lctn) {
                    case 0:
                        // Table and database names are stored on disk using the lettercase specified in the CREATE TABLE or CREATE DATABASE statement.
                        // Name comparisons are case sensitive.
                        // You should not set this variable to 0 if you are running MySQL on a system that has case-insensitive file names (such as Windows or OS X).
                        // If you force this variable to 0 with --lower-case-table-names=0 on a case-insensitive file system and access MyISAM tablenames using different lettercases, index corruption may result.
                        $result = new PreconditionResult(PreconditionResult::STATE_PASSED, t('Table names are stored in the specified lettercase (lookups are performed in a case-sensitive way).'));
                        break;
                    case 1:
                        // Table names are stored in lowercase on disk and name comparisons are not case-sensitive.
                        // MySQL converts all table names to lowercase on storage and lookup.
                        // This behavior also applies to database names and table aliases.
                        $result = new PreconditionResult(PreconditionResult::STATE_FAILED, t('Table names are stored in the lowercase: you may have problems if you plan to move this installation to another server (hint: you should set the %1$s MySQL variable to %2$s)', 'lower_case_table_names', 2));
                        break;
                    case 2:
                        // Table and database names are stored on disk using the lettercase specified in the CREATE TABLE or CREATE DATABASE statement, but MySQL converts them to lowercase on lookup.
                        // Name comparisons are not case sensitive.
                        // This works only on file systems that are not case-sensitive!
                        // InnoDB table names are stored in lowercase, as for lower_case_table_names=1.
                        $result = new PreconditionResult(PreconditionResult::STATE_PASSED, t('Table names are stored in the specified lettercase (lookups are performed in a case-insensitive way).'));
                        break;
                    default:
                        $result = new PreconditionResult(PreconditionResult::STATE_WARNING, t('Unknown value (%1$s) of the %2$s MySQL variable', $lctn, 'lower_case_table_names'));
                        break;
                }
            }
        } catch (Exception $x) {
            $result = new PreconditionResult(PreconditionResult::STATE_WARNING, $x->getMessage());
        }

        return $result;
    }
}
