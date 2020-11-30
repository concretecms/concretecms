<?php
namespace Concrete\Core\Command\Task\Runner\Traits;

use Concrete\Core\Entity\Command\Process;

defined('C5_EXECUTE') or die("Access Denied.");

trait GetProcessLogFileNameTrait
{

    /**
     * @var Process
     */
    protected $process;

    public function getLogFileName(): string
    {
        $date = date('Y-m-d-H-i', $this->process->getDateStarted());
        return snake_case($this->process->getName()) . '-' . $date . '-' . $this->process->getID() . '.log';
    }
}
