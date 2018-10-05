<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class FullUnicodeDatabaseSupport implements ConnectionOptionsPreconditionInterface
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
        return t('Database should support all Unicode characters');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'database_fullunicode';
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
     * Get a list of recommended database character sets.
     *
     * @return string[]
     */
    public function getRecommendedCharsets()
    {
        return [
            'utf8mb4',
            'utf32',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        try {
            $charset = (string) $this->connection->fetchColumn('select @@character_set_database');
        } catch (Exception $x) {
            $charset = '';
        }
        if ($charset === '') {
            return new PreconditionResult(PreconditionResult::STATE_WARNING, t('Failed to determine the default character set of the database.'));
        }
        $recommendedCharsets = $this->getRecommendedCharsets();
        if (in_array(strtolower($charset), array_map('strtolower', $recommendedCharsets), true)) {
            return new PreconditionResult(PreconditionResult::STATE_PASSED);
        }

        return new PreconditionResult(
            PreconditionResult::STATE_FAILED,
            t(
                "The default character set of the database is %s, but it's recommended to use %s in order to support all the Unicode characters.",
                $charset,
                \Punic\Misc::joinOr($recommendedCharsets)
            )
        );
    }
}
