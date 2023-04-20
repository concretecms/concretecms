<?php
namespace Concrete\Core\Install\StartingPoint\Controller;

use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;

interface ControllerInterface
{

    public function getStartingPointName(): string;

    public function getStartingPointHandle(): string;

    /**
     * @return string
     */
    public function getStartingPointThumbnail(): ?string;

    /**
     * @return string[]|string
     */
    public function getStartingPointDescription();

    public function getStartingPointInstaller(): InstallerInterface;

}
