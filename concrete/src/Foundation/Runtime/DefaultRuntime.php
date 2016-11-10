<?php
namespace Concrete\Core\Foundation\Runtime;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Foundation\Runtime\Boot\BootInterface;
use Concrete\Core\Foundation\Runtime\Run\RunInterface;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Application\ApplicationAwareTrait;

class DefaultRuntime implements RuntimeInterface, ApplicationAwareInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ENDED = 2;

    use ApplicationAwareTrait;

    /** @var string */
    protected $boot_class;

    /** @var string */
    protected $run_class;

    /** @var int */
    protected $status;

    /**
     * DefaultRuntime constructor.
     *
     * @param string $boot_class The class to use for the booter
     * @param string $run_class The class to use for the runner
     */
    public function __construct(
        $boot_class = 'Concrete\Core\Foundation\Runtime\Boot\DefaultBooter',
        $run_class = 'Concrete\Core\Foundation\Runtime\Run\DefaultRunner')
    {
        $this->boot_class = $boot_class;
        $this->run_class = $run_class;
    }

    /**
     * @return BootInterface
     */
    protected function getBooter()
    {
        return $this->app->make($this->boot_class);
    }

    /**
     * @return RunInterface
     */
    protected function getRunner()
    {
        return $this->app->make($this->run_class);
    }

    /**
     * @param string $run_class
     */
    public function setRunClass($run_class)
    {
        $this->run_class = $run_class;
    }

    /**
     * @param string $boot_class
     */
    public function setBootClass($boot_class)
    {
        $this->boot_class = $boot_class;
    }

    /**
     * Initialize the environment and prepare for running.
     */
    public function boot()
    {
        $booter = $this->getBooter();

        if ($response = $booter->boot()) {
            $this->sendResponse($response);
        } else {
            $this->status = self::STATUS_ACTIVE;
        }
    }

    /**
     * Begin the runtime.
     */
    public function run()
    {
        switch ($this->status) {
            case self::STATUS_ENDED:
                // We've already ended, lets just return
                return;

            case self::STATUS_INACTIVE:
                throw new \RuntimeException('Runtime has not yet booted.');
        }

        $runner = $this->getRunner();
        $response = $runner->run();

        if ($response) {
            $this->sendResponse($response);
        }

        return $response;
    }

    /**
     * The method that handles properly sending a response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function sendResponse(Response $response)
    {
        $response->send();

        // Set the status to ended
        $this->status = self::STATUS_ENDED;
    }
}
