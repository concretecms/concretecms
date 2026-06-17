<?php

namespace Concrete\Core\Board\Instance\Slot\Content\Populator;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractPopulator implements PopulatorInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;
}
