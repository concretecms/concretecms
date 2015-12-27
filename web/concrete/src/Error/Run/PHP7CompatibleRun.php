<?php
namespace Concrete\Core\Error\Run;

use Exception;
use InvalidArgumentException;
use Whoops\Exception\ErrorException;
use Whoops\Exception\Inspector;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\Handler;
use Whoops\Handler\HandlerInterface;
use Whoops\Run;

class PHP7CompatibleRun
{

    protected $run;

    public function __construct(Run $run)
    {
        $this->run = $run;
    }

    public function handleException($exception)
    {
        // Convert to a compatible exception
        return $this->run->handleException(PHP7CompatibleException::fromThrowable($exception));
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            $this->{$name}(...$arguments);
        } else {
            $this->run->{$name}(...$arguments);
        }
    }

    /**
     * Registers this instance as an error handler.
     * @return Run
     */
    public function register()
    {
        if (!$this->isRegistered) {
            // Workaround PHP bug 42098
            // https://bugs.php.net/bug.php?id=42098
            class_exists("\\Whoops\\Exception\\ErrorException");
            class_exists("\\Whoops\\Exception\\FrameCollection");
            class_exists("\\Whoops\\Exception\\Frame");
            class_exists("\\Whoops\\Exception\\Inspector");

            set_error_handler(array($this, Run::ERROR_HANDLER));
            set_exception_handler(array($this, Run::EXCEPTION_HANDLER));
            register_shutdown_function(array($this, Run::SHUTDOWN_HANDLER));

            $this->isRegistered = true;
        }

        return $this;
    }

    /**
     * Unregisters all handlers registered by this Whoops\Run instance
     * @return Run
     */
    public function unregister()
    {
        if ($this->isRegistered) {
            restore_exception_handler();
            restore_error_handler();

            $this->isRegistered = false;
        }

        return $this;
    }

}
