<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class PhpVersion implements PreconditionInterface
{
    /**
     * The minimum PHP version.
     *
     * @var string
     */
    const MINIMUM_PHP_VERSION = '5.5.9';

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return sprintf('PHP %s', static::MINIMUM_PHP_VERSION);
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'php_version';
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $result = new PreconditionResult();
        if (version_compare(PHP_VERSION, static::MINIMUM_PHP_VERSION) < 0) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('concrete5 requires at least PHP version %1$s (you are running PHP version %2$s).', static::MINIMUM_PHP_VERSION, PHP_VERSION))
            ;
        } else {
            $result->setMessage(t('you are running PHP version %s', PHP_VERSION));
        }

        return $result;
    }
}
