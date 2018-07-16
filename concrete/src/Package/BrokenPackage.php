<?php

namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;

class BrokenPackage extends Package
{
    public function __construct($pkgHandle, Application $application)
    {
        $this->pkgHandle = $pkgHandle;
        $this->pkgVersion = '0.0';
        $this->pkgName = 'Unknown Package';
        $this->pkgDescription = sprintf('Broken package (handle %s).', $pkgHandle);
        parent::__construct($application);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageName()
     */
    public function getPackageName()
    {
        return t('Unknown Package');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageDescription()
     */
    public function getPackageDescription()
    {
        return t('Broken package (handle %s).', $this->getPackageHandle());
    }

    public function install()
    {
        throw new UserMessageException($this->getInstallErrorMessage());
    }

    public function getInstallErrorMessage()
    {
        return t('Unable to install %s. Please check that this package has been updated for 5.7.', $this->pkgHandle);
    }
}
