<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Install\StartingPoint\Controller\ControllerInterface;
use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;

class StartingPoint extends AbstractStartingPoint
{

    /**
     * @var ControllerInterface
     */
    protected $controller;

    /**
     * StartingPoint constructor.
     */
    public function __construct(string $directory, ControllerInterface $controller)
    {
        $this->directory = $directory;
        $this->controller = $controller;
    }

    public function getHandle(): string
    {
        return $this->controller->getHandle();
    }

    public function getName(): string
    {
        return $this->controller->getName();
    }

    public function getThumbnail(): ?string
    {
        return $this->controller->getThumbnail();
    }

    public function providesThumbnails(): bool
    {
        return $this->controller->providesThumbnails();
    }

    public function getDescription()
    {
        return $this->controller->getDescription();
    }

    public function getInstaller(): InstallerInterface
    {
        return $this->controller->getInstaller();
    }

}
