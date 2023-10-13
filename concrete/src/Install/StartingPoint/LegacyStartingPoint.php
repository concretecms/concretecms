<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Install\StartingPoint\Installer\Installer;
use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;
use Concrete\Core\Package\StartingPointPackage;

class LegacyStartingPoint extends AbstractStartingPoint
{

    /**
     * @var StartingPointPackage
     */
    protected $controller;

    public function __construct(string $directory, StartingPointPackage $controller)
    {
        $this->directory = $directory;
        $this->controller = $controller;
    }

    public function getHandle(): string
    {
        return $this->controller->getPackageHandle();
    }

    public function getName(): string
    {
        return $this->controller->getPackageName();
    }

    public function getThumbnail(): ?string
    {
        return null;
    }

    public function providesThumbnails(): bool
    {
        return $this->controller->contentProvidesFileThumbnails();
    }

    public function getDescription()
    {
        return $this->controller->getPackageDescription();
    }

    public function getInstaller(): InstallerInterface
    {
        return new Installer();
    }

}
