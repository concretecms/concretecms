<?php
namespace Concrete\Core\Cache\Level;

use Concrete\Core\Cache\Cache;
use Config;
/**
 * Class ExpensiveCache
 * This cache stores data that is expensive to build that will see a performance boost if stored on disk.
 *
 * \@package Concrete\Core\Cache\Level
 */
class ExpensiveCache extends Cache
{
}
