<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine;

use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;
use Concrete\Core\Install\InstallerOptions;

interface InstallOptionsAwareInterface
{

    public function setInstallOptions(InstallerOptions $options): void;

    public function getInstallOptions(): InstallerOptions;

}
