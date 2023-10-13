<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Stamp;

use Concrete\Core\Install\InstallerOptions;
use Symfony\Component\Messenger\Stamp\StampInterface;

class InstallOptionsStamp implements StampInterface
{

    /**
     * @var InstallerOptions
     */
    protected $installOptions;

    /**
     * InstallOptionsStamp constructor.
     * @param InstallerOptions $installOptions
     */
    public function __construct(InstallerOptions $installOptions)
    {
        $this->installOptions = $installOptions;
    }

    /**
     * @return InstallerOptions
     */
    public function getInstallOptions(): InstallerOptions
    {
        return $this->installOptions;
    }



}
