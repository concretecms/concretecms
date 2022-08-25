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




}
