<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractContext implements ContextInterface
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @return MessageBusInterface
     */
    public function getMessageBus(): MessageBusInterface
    {
        return $this->messageBus;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }



    /**
     * AbstractContext constructor.
     * @param MessageBusInterface $messageBus
     * @param OutputInterface $output
     */
    public function __construct(MessageBusInterface $messageBus, OutputInterface $output)
    {
        $this->messageBus = $messageBus;
        $this->output = $output;
    }


}
