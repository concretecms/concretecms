<?php
namespace Concrete\Core\Command\Batch;

/**
 * Batch builder object for use before the batch is actually dispatched and converted into an entity.
 */
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
     * @param iterable|callable $messages
     */
    public function setMessages($messages)
    {
        if (is_callable($messages)) {
            $messages = $messages();
        }
        $this->messages = $messages;
        return $this;
    }

    /**
     * @param iterable|callable $messages
     * @param string $name
     * @return Batch
     */
    public static function create($messages, string $name)
    {
        $batch = new self();
        $batch->setName($name);
        $batch->setMessages($messages);
        return $batch;
    }



}
