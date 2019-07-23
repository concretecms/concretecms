<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Database\CharacterSetCollation\Exception as CharacterSetCollationException;
use Concrete\Core\Database\CharacterSetCollation\Exception\LongKeysUnsupportedByCollation;
use Concrete\Core\Database\CharacterSetCollation\Exception\NoCharacterSetCollationDefinedException;
use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\PreconditionResult;
use Exception;

class DatabaseCharsetCollation implements ConnectionOptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Database\CharacterSetCollation\Resolver
     */
    protected $characterSetCollationResolver;

    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * @var \Concrete\Core\Database\Connection\Connection|null
     */
    protected $connection;

    /**
     * @param \Concrete\Core\Database\CharacterSetCollation\Resolver $characterSetCollationResolver
     */
    public function __construct(Resolver $characterSetCollationResolver)
    {
        $this->characterSetCollationResolver = $characterSetCollationResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Database should support the configured character set and collation');
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
        try {
            list($charset, $collation) = $this->characterSetCollationResolver->resolveCharacterSetAndCollation($this->connection);
        } catch (NoCharacterSetCollationDefinedException $x) {
            return new PreconditionResult(PreconditionResult::STATE_SKIPPED, t('preferred database character set and collation are not configured.'));
        } catch (CharacterSetCollationException $x) {
            if ($x instanceof LongKeysUnsupportedByCollation && $x->getCollation() === 'utf8mb4_unicode_ci' && $x->getWantedKeyLength() === 255) {
                // Let's simplify a bit the error message for the default concrete5 values
                // If users customize these values, let's keep the original (complex) message (assuming that they know what they are doing)
                return new PreconditionResult(PreconditionResult::STATE_FAILED,
                    t('The database does not have a complete support for all the Unicode characters.')
                    . ' ' . t("This implies, for example, that you may not be able to save emoticons (it's the same behavior as concrete5 versions older than 8.5.0).")
                    . ' ' . t('You should upgrade your database to fix this issue.')
                );
            }

            return new PreconditionResult(PreconditionResult::STATE_FAILED, $x->getMessage());
        } catch (Exception $x) {
            return new PreconditionResult(PreconditionResult::STATE_SKIPPED, $x->getMessage());
        }

        return new PreconditionResult(PreconditionResult::STATE_PASSED, t('The database supports the "%1$s" character set and the "%2$s" collation.', $charset, $collation));
    }
}
