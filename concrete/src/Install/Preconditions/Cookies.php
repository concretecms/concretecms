<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\AbstractListablePrecondition;

class Cookies extends AbstractListablePrecondition
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Cookies Enabled');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'cookies';
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
     * @see WebPreconditionInterface::getInitialState()
     */
    public function getInitialState()
    {
        return null;
    }


    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        return null;
    }

    public function getComponent(): string
    {
        return 'cookies-precondition';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['message_failed'] = t('Cookies must be enabled in your browser to install Concrete CMS.');
        return $data;
    }
}
