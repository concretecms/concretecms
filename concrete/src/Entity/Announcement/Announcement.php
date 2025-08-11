<?php

namespace Concrete\Core\Entity\Announcement;

use Concrete\Core\Announcement\Controller\ControllerInterface;
use Concrete\Core\Announcement\Manager;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="Announcements")
 */
class Announcement implements \JsonSerializable
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $handle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateCreated;

    /**
     * Introduction constructor.
     */
    public function __construct()
    {
        $this->dateCreated = time();
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
     * @return int
     */
    public function getDateCreated(): int
    {
        return $this->dateCreated;
    }

    /**
     * @param int $dateCreated
     */
    public function setDateCreated(int $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return ControllerInterface
     */
    public function getController(): ControllerInterface
    {
        $manager = app(Manager::class);
        return $manager->driver($this->getHandle());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'dateCreated' => $this->getDateCreated(),
            'handle' => $this->getHandle(),
        ];
    }

}
