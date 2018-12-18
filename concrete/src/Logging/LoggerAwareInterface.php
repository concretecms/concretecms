<?php
namespace Concrete\Core\Logging;

use Psr\Log\LoggerAwareInterface as PsrLoggerAwareInterface;

/**
 * Interface LoggerAwareInterface
 */
interface LoggerAwareInterface extends PsrLoggerAwareInterface
{

    public function getLoggerChannel();

}
