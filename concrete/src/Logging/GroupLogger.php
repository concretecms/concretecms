<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Facade;
use Monolog\Logger as Monolog;

class GroupLogger
{
    protected $level;
    protected $messages = array();

    public function __construct($channel = false, $level = Logger::DEBUG)
    {
        $app = Facade::getFacadeApplication();
        $loggerFactory = $app->make(LoggerFactory::class);
        $this->logger = $loggerFactory->createLogger($channel);
        $this->level = $level;
    }

    public function write($message)
    {
        $this->messages[] = $message;
    }

    public function close($context = array())
    {
        $method = 'add' . ucfirst(strtolower(Monolog::getLevelName($this->level)));
        $arguments = array(implode("\n", $this->messages), $context);

        return call_user_func_array(array($this->logger, $method), $arguments);
    }
}
