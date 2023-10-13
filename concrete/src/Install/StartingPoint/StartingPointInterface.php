<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Install\StartingPoint\Controller\ControllerInterface;
use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;

interface StartingPointInterface extends ControllerInterface, \JsonSerializable
{

    public function getDirectory(): string;

    public function getInstaller(): InstallerInterface;

}