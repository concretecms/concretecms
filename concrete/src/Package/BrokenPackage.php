<?php

namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;

class BrokenPackage extends Package
{
    /**
     * @var string
     */
    protected $pkgHandle;

    /**
     * @var string
     */
    protected $pkgVersion;

    /**
     * @var string
     */
    protected $pkgName;

    /**
     * @var string
     */
    protected $pkgDescription;

    /**
     * @var string
     */
    private $errorDetails;

    /**
     * @param string $pkgHandle
     */
    public function __construct($pkgHandle, Application $application, string $errorDetails = '')
    {
        $this->pkgHandle = $pkgHandle;
        $this->pkgVersion = '0.0';
        $this->pkgName = 'Unknown Package';
        $this->pkgDescription = sprintf('Broken package (handle %s).', $pkgHandle);
        $this->errorDetails = $errorDetails;
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
        $result = t('Unable to install %s. Make sure it has a valid controller.php file and that it has been updated for Concrete 5.7.0 and later.', $this->pkgHandle);
        if ($this->errorDetails !== '') {
            $result .= "\n\n" . t('Error Details: %s', $this->errorDetails);
        }

        return $result;
    }
}
