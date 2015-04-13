<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class AbstractManager implements ManagerInterface, RegistryInterface
{

    protected $messages;

    public function registerMessages($messages)
    {
        foreach($messages as $identifier => $message) {
            $this->registerMessageString($identifier, $message);
        }
    }

    public function registerMessageString($identifier, $message)
    {
        $m = new Message();
        $m->setMessageContent($message);
        $m->setIdentifier($identifier);
        $this->messages[$identifier] = $m;
    }

    public function getMessage($identifier)
    {
        return $this->messages[$identifier];
    }

    public function getFormatter(Message $message)
    {
        return new Formatter();
    }


}
