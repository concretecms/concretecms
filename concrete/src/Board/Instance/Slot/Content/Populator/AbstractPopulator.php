<?php

namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements LoggerAwareInterface, PopulatorInterface, ApplicationAwareInterface
{

    use LoggerAwareTrait;
    use ApplicationAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

}
