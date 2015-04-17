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
        if (is_array($message)) {
            $m->setMessageContent($message[0]);
            $m->addGuide($message[1]);
        } else {
            $m->setMessageContent($message);
        }
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
