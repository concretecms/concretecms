<?php
namespace Concrete\Core\Cache\Level;

use Stash\Driver\Ephemeral;
use Stash\Pool;

/**
 * Class RequestCache
 * Cache that only lives for the current request. This cache level is not configurable.
 * @package Concrete\Core\Cache\Level
 */
class RequestCache extends \Cache
{
    protected function init()
    {
        $this->pool = new Pool(new Ephemeral());
        $this->enable();
    }
}