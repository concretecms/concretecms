<?php

namespace Concrete\Core\Logging;

use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;

/**
 * Trait LoggerAwareTrait
 * A trait used with LoggerAwareInterface
 */
trait LoggerAwareTrait
{

    use PsrLoggerAwareTrait;

    /**
     * Get the logger channel expected by this LoggerAwareTrait implementation
     * The user is expected to declare this method and return a valid channel name.
     *
     * @return string One of \Concrete\Core\Logging\Channels::CHANNEL_*
     */
    abstract public function getLoggerChannel();

}
