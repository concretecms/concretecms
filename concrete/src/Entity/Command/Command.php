<?php

namespace Concrete\Core\Entity\Command;

use Concrete\Core\Automation\Command\Controller\ControllerInterface;
use Concrete\Core\Automation\Command\Manager;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Commands")
 */
class Command implements \JsonSerializable
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
    protected $dateLastRun;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $lastRunBy;

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
    public function getDateLastRun()
    {
        return $this->dateLastRun;
    }

    /**
     * @param mixed $dateLastRun
     */
    public function setDateLastRun($dateLastRun): void
    {
        $this->dateLastRun = $dateLastRun;
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
     * @return ControllerInterface
     */
    public function getController()
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }

    public function jsonSerialize()
    {
        $controller = $this->getController();
        $data = [
            'id' => $this->getID(),
            'name' => $controller->getName(),
            'description' => $controller->getDescription(),
            'help' => $controller->getHelpText(),
        ];
        return $data;
    }


}
