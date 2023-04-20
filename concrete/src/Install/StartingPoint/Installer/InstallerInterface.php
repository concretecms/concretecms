<?php
namespace Concrete\Core\Install\StartingPoint\Installer;

use Concrete\Core\Install\InstallerOptions;

interface InstallerInterface
{

    /**
     * @return
     */
    public function getInstallCommands(InstallerOptions $options): array;

}
