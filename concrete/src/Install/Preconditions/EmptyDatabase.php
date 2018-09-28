<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class EmptyDatabase implements ConnectionOptionsPreconditionInterface
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
        return t('Database is empty');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'empty_database';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
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
        if ($this->installerOptions->isAutoAttachEnabled() === false) {
            try {
                $existingTables = [];
                foreach ($this->connection->fetchAll('show tables') as $row) {
                    $existingTables[] = array_shift($row);
                }
                $numExistingTables = count($existingTables);
                if ($numExistingTables > 0) {
                    $result = new PreconditionResult(
                        PreconditionResult::STATE_FAILED,
                        t2(
                            'There is already %s table in this database. concrete5 must be installed in an empty database.',
                            'There are already %s tables in this database. concrete5 must be installed in an empty database.',
                            $numExistingTables
                        )
                    );
                } else {
                    $result = new PreconditionResult(PreconditionResult::STATE_PASSED);
                }
            } catch (Exception $x) {
                $result = new PreconditionResult(PreconditionResult::STATE_FAILED, $x->getMessage());
            }
        } else {
            $result = new PreconditionResult(PreconditionResult::STATE_SKIPPED, t('The configuration is auto-attached'));
        }

        return $result;
    }
}
