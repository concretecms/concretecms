<?php
namespace Concrete\Core\Logging;

class GroupLogger
{
    protected $level;
    protected $messages = array();

    public function __construct($channel = false, $level = Logger::DEBUG)
    {
        $this->logger = new Logger($channel);
        $this->level = $level;
    }

    public function write($message)
    {
        $this->messages[] = $message;
    }

    public function close($context = array())
    {
        $method = 'add' . ucfirst(strtolower(Logger::getLevelName($this->level)));
        $arguments = array(implode("\n", $this->messages), $context);

        return call_user_func_array(array($this->logger, $method), $arguments);
    }
}
