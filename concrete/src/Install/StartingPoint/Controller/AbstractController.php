<?php
namespace Concrete\Core\Install\StartingPoint\Controller;

use Concrete\Core\Install\StartingPoint\Installer\Installer;
use Concrete\Core\Install\StartingPoint\Installer\InstallerInterface;

abstract class AbstractController implements ControllerInterface
{

    public function providesThumbnails(): bool
    {
        return false;
    }

    public function getInstaller(): InstallerInterface
    {
        return new Installer();
    }

}