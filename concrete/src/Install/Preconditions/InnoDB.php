<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class InnoDB implements ConnectionOptionsPreconditionInterface
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
        return t('MySQL InnoDB engine');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'innodb';
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
        try {
            $supported = false;
            foreach ($this->connection->fetchAll('show engines') as $engine) {
                $engine = array_change_key_case($engine, CASE_LOWER);
                if (isset($engine['engine']) && strtolower($engine['engine']) == 'innodb') {
                    $supported = true;
                    break;
                }
            }
            if ($supported) {
                $result = new PreconditionResult(PreconditionResult::STATE_PASSED);
            } else {
                $result = new PreconditionResult(PreconditionResult::STATE_FAILED, t('Your MySQL database does not support InnoDB database tables. These are required.'));
            }
        } catch (Exception $x) {
            $result = new PreconditionResult(PreconditionResult::STATE_FAILED, $x->getMessage());
        }

        return $result;
    }
}
