<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Facade;
use Monolog\Logger as Monolog;

class GroupLogger
{
    protected $level;
    protected $messages = [];

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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

        return call_user_func([$this->logger, $method], implode("\n", $this->messages), $context);
    }
}
