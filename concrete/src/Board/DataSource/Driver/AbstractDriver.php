<?php
namespace Concrete\Core\Board\DataSource\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractDriver implements DriverInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

}
