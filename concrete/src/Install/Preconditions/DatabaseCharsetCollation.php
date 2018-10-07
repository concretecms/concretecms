<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class DatabaseCharsetCollation implements ConnectionOptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * @var \Concrete\Core\Database\Connection\Connection|null
     */
    protected $connection;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Database should support preferred character set and collation');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'database_charset_collation';
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
        $charset = strtolower((string) $this->config->get('database.preferred_character_set', ''));
        if ($charset === '') {
            return new PreconditionResult(PreconditionResult::STATE_SKIPPED, t('preferred database character set is not configured.'));
        }
        try {
            $availableCharsets = $this->connection->getSupportedCharsets();
        } catch (Exception $x) {
            return new PreconditionResult(PreconditionResult::STATE_SKIPPED, t('Failed to retrieve the available database character sets.'));
        }
        if (!isset($availableCharsets[$charset])) {
            return new PreconditionResult(PreconditionResult::STATE_FAILED, t('The database character set "%s" is not available.', $charset));
        }
        $collation = strtolower((string) $this->config->get('database.preferred_collation', ''));
        if ($collation === '') {
            return new PreconditionResult(PreconditionResult::STATE_PASSED, t('The database supports the "%s" character set.', $charset));
        }
        if ($collation !== $availableCharsets[$charset]) {
            try {
                $availableCollations = $this->connection->getSupportedCollations();
            } catch (Exception $x) {
                return new PreconditionResult(PreconditionResult::STATE_SKIPPED, t('Failed to retrieve the available database collations.'));
            }
            if (!isset($availableCollations[$collation])) {
                return new PreconditionResult(PreconditionResult::STATE_FAILED, t('The database collation "%s" is not available.', $collation));
            }
        }

        return new PreconditionResult(PreconditionResult::STATE_PASSED, t('The database supports the "%1$s" character set and the "%2$s" collation.', $charset, $collation));
    }
}
