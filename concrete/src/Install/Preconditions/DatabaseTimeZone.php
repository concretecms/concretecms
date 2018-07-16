<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Connection\Timezone;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;
use Punic\Misc as PunicMisc;

class DatabaseTimeZone implements ConnectionOptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $application;

    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * @var \Concrete\Core\Database\Connection\Connection|null
     */
    protected $connection;

    /**
     * Initialize the instance.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Database time zone');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'database_timezone';
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
            $serverTimeZone = $this->installerOptions->getServerTimeZone(false);
            $ctz = $this->application->make(Timezone::class, ['connection' => $this->connection]);
            $deltaTimezone = $ctz->getDeltaTimezone($serverTimeZone);
            if ($deltaTimezone !== null) {
                $error = $ctz->describeDeltaTimezone($deltaTimezone);
                $suggestTimezones = $ctz->getCompatibleTimezones();
                if (!empty($suggestTimezones)) {
                    $error .= ' ' . t('These are the time zones compatible with the database one: %s', PunicMisc::join($suggestTimezones));
                }
                $result = new PreconditionResult(PreconditionResult::STATE_FAILED, $error);
            } else {
                $result = new PreconditionResult(PreconditionResult::STATE_PASSED);
            }
        } catch (Exception $x) {
            $result = new PreconditionResult(PreconditionResult::STATE_WARNING, $x->getMessage());
        }

        return $result;
    }
}
