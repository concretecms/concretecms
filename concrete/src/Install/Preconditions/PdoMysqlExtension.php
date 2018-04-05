<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class PdoMysqlExtension implements PreconditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('MySQL PDO Extension Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'pdo_mysql_extension';
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
        if (!(
            extension_loaded('pdo')
            && extension_loaded('pdo_mysql')
        )) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('The PDO extension is not loaded.'))
            ;
        }

        return $result;
    }
}
