<?php

namespace Concrete\Core\Install;

/**
 * Interface for the checks to be performed before installing concrete5 but after the configuration has been specified.
 * @since 8.4.0
 */
interface OptionsPreconditionInterface extends PreconditionInterface
{
    /**
     * Set the installer options to be checked.
     *
     * @param InstallerOptions $installerOptions
     *
     * @return $this
     */
    public function setInstallerOptions(InstallerOptions $installerOptions);
}
