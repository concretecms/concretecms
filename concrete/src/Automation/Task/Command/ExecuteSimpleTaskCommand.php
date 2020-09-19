<?php
namespace Concrete\Core\Automation\Task\Command;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Foundation\Command\CommandInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ExecuteSimpleTaskCommand extends Command implements TaskRunnerCommandInterface
{

    /**
     * @var CommandInterface
     */
    protected $command;

    /**
     * @var string
     */
    protected $responseMessage;

    public function __construct(CommandInterface $command, string $responseMessage)
    {
        $this->command = $command;
        $this->responseMessage = $responseMessage;
    }

    /**
     * @return CommandInterface
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function getTaskRunnerResponse(): ResponseInterface
    {
        return new TaskCompletedResponse($this->getResponseMessage());
    }


}
