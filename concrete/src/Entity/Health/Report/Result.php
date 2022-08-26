<?php

namespace Concrete\Core\Entity\Health\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="HealthReportResults")
 */
class Result
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Command\Process")
     * @ORM\JoinColumn(name="processID", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $process;

    /**
     * @ORM\OneToMany(targetEntity="Finding", mappedBy="result", cascade={"remove"})
     * @ORM\JoinColumn(name="resultFindingID", referencedColumnName="id")
     */
    protected $findings;

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
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param mixed $process
     */
    public function setProcess($process): void
    {
        $this->process = $process;
    }

    public function getName(): string
    {
        return $this->process->getName();
    }

    public function getTotalFindings(): int
    {
        return $this->findings->count();
    }

    public function getDateStarted($mask = null)
    {
        $timestamp = $this->getProcess()->getDateStarted();
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
        $timestamp = $this->getProcess()->getDateCompleted();
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
            }
            if ($a instanceof WarningFinding && $b instanceof SuccessFinding) {
                return -1;
            }
            if ($a instanceof SuccessFinding && $b instanceof InfoFinding) {
                return -1;
            }
            return 1;
        });
        return $findings;
    }

}
