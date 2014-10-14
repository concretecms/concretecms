<?php
namespace Concrete\Core\Package;

class BrokenPackage extends Package
{

    public function __construct($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
        $this->pkgVersion = '0.0';
        $this->pkgName = t('Unknown Package');
        $this->pkgDescription = t('Broken package (handle %s).', $pkgHandle);
    }

    public function install()
    {
        throw new \Exception($this->getInstallErrorMessage());
    }

    public function getInstallErrorMessage()
    {
        return t('Unable to install %s. Please check that this package has been updated for 5.7.', $this->pkgHandle);
    }

}
