<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class DomExtension implements PreconditionInterface
{
    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('DOM Extension Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'dom_extension';
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
        if (!extension_loaded('dom')) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t("You must enable PHP's DOM support."))
            ;
        }

        return $result;
    }
}
