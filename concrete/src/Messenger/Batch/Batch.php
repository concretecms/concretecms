<?php
namespace Concrete\Core\Messenger\Batch;

class Batch
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var iterable
     */
    protected $messages;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return iterable
     */
    public function getMessages(): iterable
    {
        return $this->messages;
    }

    /**
     * @param iterable $messages
     */
    public function setMessages(iterable $messages)
    {
        $this->messages = $messages;
        return $this;
    }





}
