<?php

namespace Concrete\Core\Logging\Handler\ErrorEnhancer;

use Symfony\Component\ErrorHandler\ErrorEnhancer\ErrorEnhancerInterface;
use Concrete\Core\Error\UserMessageException;

class UserMessageExceptionEnhancer implements ErrorEnhancerInterface
{
    /**
     * Don't touch UserMessageException exceptions.
     *
     * {@inheritdoc}
     *
     * @see \Symfony\Component\ErrorHandler\ErrorEnhancer\ErrorEnhancerInterface::enhance()
     */
    public function enhance(\Throwable $error): ?\Throwable
    {
        return $error instanceof UserMessageException ? $error : null;
    }
}
