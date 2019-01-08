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

    abstract public function getLoggerChannel();

}
