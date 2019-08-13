<?php
namespace Concrete\Core\Logging;

use Psr\Log\LoggerAwareInterface as PsrLoggerAwareInterface;

/**
 * Interface LoggerAwareInterface
 * @since 8.5.0
 */
interface LoggerAwareInterface extends PsrLoggerAwareInterface
{

    public function getLoggerChannel();

}
