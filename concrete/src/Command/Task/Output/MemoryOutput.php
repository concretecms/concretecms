<?php

namespace Concrete\Core\Command\Task\Output;

defined('C5_EXECUTE') or die('Access Denied.');

class MemoryOutput implements OutputInterface
{
    /**
     * @var string[]
     */
    protected $messages = [];

    /**
     * @var callable|null
     */
    protected $listener;

    public function __construct(?callable $listener = null)
    {
        $this->listener = $listener;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Output\OutputInterface::write()
     */
    public function write($message): void
    {
        $message = (string) $message;
        $this->messages[] = $message;
        if ($this->listener !== null) {
            ($this->listener)($message, $this);
        }
    }

    public function writeError($message): void
    {
        $this->write($message);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->messages = [];

        return $this;
    }
}
