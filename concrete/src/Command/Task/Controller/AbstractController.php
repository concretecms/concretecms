<?php
namespace Concrete\Core\Command\Task\Controller;

use Concrete\Core\Command\Task\Input\Definition\Definition;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractController implements ControllerInterface
{

    public function getHelpText(): string
    {
        return '';
    }

    public function getConsoleCommandName(): string
    {
        return snake_case($this->getName(), '-');
    }

    public function getInputDefinition(): ?Definition
    {
        return null;
    }

}
