<?php

namespace Concrete\Core\Entity\Health\Report;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Health\Grade\GradeInterface;
use Concrete\Core\Health\Report\Result\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Result\Formatter\StandardFormatter;
use Concrete\Core\Health\Report\Result\ResultInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="HealthReportResults")
 */
class Result implements ResultInterface
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Automation\Task")
     * @ORM\JoinColumn(name="taskID", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $task;

    /**
     * @ORM\OneToMany(targetEntity="Finding", mappedBy="result", cascade={"remove"})
     * @ORM\JoinColumn(name="resultFindingID", referencedColumnName="id")
     */
    protected $findings;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $dateStarted;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $dateCompleted;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $score;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $grade;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Task|null
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $dateStarted
     */
    public function setDateStarted($dateStarted): void
    {
        $this->dateStarted = $dateStarted;
    }

    /**
     * @param mixed $dateCompleted
     */
    public function setDateCompleted($dateCompleted): void
    {
        $this->dateCompleted = $dateCompleted;
    }

    public function getTotalFindings(): int
    {
        return $this->findings->count();
    }

    public function getDateStarted($mask = null)
    {
        $timestamp = $this->dateStarted;
        if ($timestamp) {
            if ($mask) {
                return (new \DateTime())->setTimestamp($timestamp)->format($mask);
            } else {
                return $timestamp;
            }
        }
        return null;
    }

    public function getDateCompleted($mask = null)
    {
        $timestamp = $this->dateCompleted;
        if ($timestamp) {
            if ($mask) {
                return (new \DateTime())->setTimestamp($timestamp)->format($mask);
            } else {
                return $timestamp;
            }
        }
        return null;
    }

    /**
     * @return Finding[]
     */
    public function getFindings()
    {
        return $this->findings;
    }

    /**
     * Returns all findings for the current report result, but weights them, with items by type coming in the following
     * positions
     *
     * AlertFinding
     * WarningFinding
     * SuccessFinding
     * InfoFinding
     *
     * @return Finding[]
     */
    public function getWeightedFindings(): array
    {
        $findings = $this->getFindings()->toArray();
        usort($findings, function(Finding $a, Finding $b) {
            if (get_class($a) === get_class($b)) {
                return 0;
            }

            if ($a instanceof AlertFinding) {
                return -1;
            } else {
                if ($a instanceof WarningFinding && ($b instanceof SuccessFinding || $b instanceof InfoFinding)) {
                    return -1;
                }
                if ($a instanceof SuccessFinding && $b instanceof InfoFinding) {
                    return -1;
                }
                if ($a instanceof SuccessFinding && ($b instanceof WarningFinding || $b instanceof AlertFinding)) {
                    return 1;
                }

                return 1;
            }
        });
        return $findings;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return mixed
     */
    public function getRawGrade()
    {
        return $this->grade;
    }

    public function getGrade(): ?GradeInterface
    {
        if (is_array($this->grade) && isset($this->grade['class'])) {
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            return $serializer->denormalize($this->grade, $this->grade['class']);
        }
        return null;
    }

    public function getFormatter(): FormatterInterface
    {
        return new StandardFormatter();
    }
}
