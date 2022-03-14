<?php

namespace Concrete\Core\Entity\Automation;

use Carbon\Carbon;
use Concrete\Core\Command\Task\Controller\ControllerInterface;
use Concrete\Core\Command\Task\Manager;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Tasks")
 */
class Task implements \JsonSerializable, TaskInterface
{

    use PackageTrait;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $handle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateLastStarted;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateLastCompleted;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $lastRunBy;

    /**
     * @ORM\OneToMany(targetEntity="TaskSetTask", mappedBy="task", cascade={"remove"})
     */
    protected $set_tasks;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle): void
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getLastRunBy()
    {
        return $this->lastRunBy;
    }

    /**
     * @param mixed $lastRunBy
     */
    public function setLastRunBy($lastRunBy): void
    {
        $this->lastRunBy = $lastRunBy;
    }

    /**
     * @return mixed
     */
    public function getDateLastStarted()
    {
        return $this->dateLastStarted;
    }

    /**
     * @param mixed $dateLastStarted
     */
    public function setDateLastStarted($dateLastStarted): void
    {
        $this->dateLastStarted = $dateLastStarted;
    }

    /**
     * @return mixed
     */
    public function getDateLastCompleted()
    {
        return $this->dateLastCompleted;
    }

    /**
     * @param mixed $dateLastCompleted
     */
    public function setDateLastCompleted($dateLastCompleted): void
    {
        $this->dateLastCompleted = $dateLastCompleted;
    }

    /**
     * @return ControllerInterface
     */
    public function getController(): ControllerInterface
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }

    public function formatDateLastStarted($format)
    {
        $date = new Date();
        if ($this->getDateLastStarted()) {
            return $date->toDateTime('@' . $this->getDateLastStarted())
                ->format($format);
        }
    }

    public function formatDateLastCompleted($format)
    {
        $date = new Date();
        if ($this->getDateLastCompleted()) {
            return $date->toDateTime('@' . $this->getDateLastCompleted())
                ->format($format);
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $controller = $this->getController();
        $data = [
            'id' => $this->getID(),
            'name' => $controller->getName(),
            'description' => $controller->getDescription(),
            'inputDefinition' => $controller->getInputDefinition(),
            'help' => $controller->getHelpText(),
            'dateLastStarted' => $this->getDateLastStarted(),
            'dateLastCompleted' => $this->getDateLastCompleted(),
            'dateLastStartedFormatted' => $this->formatDateLastStarted('M d, Y g:i a'),
            'dateLastCompletedFormatted' => $this->formatDateLastCompleted('M d, Y g:i a'),
            'lastRunBy' => $this->getLastRunBy(),
        ];
        return $data;
    }


}
