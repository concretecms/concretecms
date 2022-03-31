<?php

namespace Concrete\Core\Entity\Command;

use Concrete\Core\Command\Task\Input\Input;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ScheduledTaskRepository")
 * @ORM\Table(name="MessengerScheduledTasks")
 */
class ScheduledTask implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $input;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Automation\Task")
     */
    protected $task;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $dateScheduled;


    /**
     * @ORM\Column(type="text")
     */
    protected $cronExpression;


    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input): void
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * @param mixed $cronExpression
     */
    public function setCronExpression($cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDateScheduled()
    {
        return $this->dateScheduled;
    }

    /**
     * @param mixed $dateScheduled
     */
    public function setDateScheduled($dateScheduled): void
    {
        $this->dateScheduled = $dateScheduled;
    }

    public function getCronExpressionObject(): CronExpression
    {
        return new CronExpression($this->getCronExpression());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $timezone = date_default_timezone_get();
        $dateScheduledString = (new \DateTime('@' . $this->getDateScheduled()))
            ->setTimezone(new \DateTimeZone($timezone))
            ->format('F d, Y g:i a');

        $cronExpression = $this->getCronExpressionObject();

        $data = [
            'id' => $this->getID(),
            'task' => $this->getTask(),
            'input' => $this->getInput(),
            'dateScheduled' => $this->getDateScheduled(),
            'dateScheduledString' => $dateScheduledString,
            'cronExpression' => $this->getCronExpression(),
            'nextRunDate' => $cronExpression->getNextRunDate()->format('Y-m-d H:i:s'),
            'user' => $this->getUser(),
        ];
        return $data;
    }

    public function getTaskInput(): InputInterface
    {
        $serializer = app(JsonSerializer::class);
        /**
         * @var $serializer Serializer
         */
        return $serializer->denormalize($this->getInput(), Input::class);
    }






}
