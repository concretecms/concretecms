<?php

namespace Concrete\Core\Entity\Update;

use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Updater\Announcement\AnnouncementService;
use Concrete\Core\Updater\Announcement\Manager;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="SystemUpdateAnnouncements")
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
     * @ORM\Column(type="string")
     */
    protected $namespace;

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
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
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


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'dateCreated' => $this->getDateCreated(),
            'namespace' => $this->getNamespace(),
            'handle' => $this->getHandle(),
        ];
    }

    public function getSlides()
    {
        $service = app(AnnouncementService::class);
        $announcement = $service->getAnnouncementDriver($this);
        return $announcement->getSlides();
    }

}
