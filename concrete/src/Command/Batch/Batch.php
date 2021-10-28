<?php
namespace Concrete\Core\Command\Batch;

use Concrete\Core\Command\Batch\Command\HandleBatchMessageCommand;
use Concrete\Core\Entity\Command\Batch as BatchEntity;

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
        if (!$this->messages) {
            return [];
        }
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

    public function add(object $message)
    {
        $this->messages[] = $message;
        return $this;
    }

    public function getWrappedMessages(BatchEntity $batchEntity)
    {
        $messages = [];
        foreach ($this->getMessages() as $message) {
            $messages[] = new HandleBatchMessageCommand($batchEntity->getID(), $message);
        }
        return $messages;
    }

    /**
     * @param string $name
     * @param iterable|callable $messages
     * @return Batch
     */
    public static function create(string $name = null, $messages = null)
    {
        $batch = new self();
        if ($name) {
            $batch->setName($name);
        }
        if ($messages) {
            $batch->setMessages($messages);
        }
        return $batch;
    }



}
