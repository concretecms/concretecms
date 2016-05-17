<?php
namespace Concrete\Core\Tools\Console\Doctrine;

use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet as HelperSet;

class ConsoleRunner extends DoctrineConsoleRunner
{
    public static function run(HelperSet $helperSet, $commands = array())
    {
        die;
    }
}
