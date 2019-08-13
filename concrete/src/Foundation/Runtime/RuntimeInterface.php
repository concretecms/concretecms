<?php
namespace Concrete\Core\Foundation\Runtime;

use Concrete\Core\Foundation\Runtime\Boot\BootInterface;
use Concrete\Core\Foundation\Runtime\Run\RunInterface;

/**
 * @since 8.0.0
 */
interface RuntimeInterface extends BootInterface, RunInterface
{
    /**
     * Initialize the environment and prepare for running.
     */
    public function boot();

    /**
     * Begin the runtime.
     */
    public function run();
}
