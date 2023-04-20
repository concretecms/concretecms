<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Traits;

use Concrete\Core\Install\InstallerOptions;

trait InstallOptionsAwareTrait
{

    /**
     * @var InstallerOptions
     */
    protected $installOptions;

    /**
     * @return InstallerOptions
     */
    public function getInstallOptions(): InstallerOptions
    {
        return $this->installOptions;
    }

    /**
     * @param InstallerOptions $installOptions
     */
    public function setInstallOptions(InstallerOptions $installOptions): void
    {
        $this->installOptions = $installOptions;
    }


}
