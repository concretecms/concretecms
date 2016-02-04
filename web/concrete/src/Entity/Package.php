<?php
namespace Concrete\Core\Entity;

use Concrete\Core\Package\LocalizablePackageInterface;

/**
 * @Entity
 * @Table(name="Packages")
 */
class Package implements LocalizablePackageInterface
{
    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $pkgID;

    /**
     * @Column(type="string", unique=true)
     */
    protected $pkgHandle;

    /**
     * @Column(type="string")
     */
    protected $pkgVersion;

    /**
     * @Column(type="boolean")
     */
    protected $pkgIsInstalled = true;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $pkgAvailableVersion;

    /**
     * @Column(type="text")
     */
    protected $pkgDescription;

    /**
     * @Column(type="datetime")
     */
    protected $pkgDateInstalled;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $pkgName;

    /**
     * @return mixed
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * @param mixed $handle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    /**
     * @return mixed
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return mixed
     */
    public function isPackageInstalled()
    {
        return $this->pkgIsInstalled;
    }

    /**
     * @param mixed $pkgIsInstalled
     */
    public function setIsPackageInstalled($pkgIsInstalled)
    {
        $this->pkgIsInstalled = $pkgIsInstalled;
    }


    /**
     * @return mixed
     */
    public function getPackageVersion()
    {
        return $this->pkgVersion;
    }

    /**
     * @param mixed $pkgVersion
     */
    public function setPackageVersion($pkgVersion)
    {
        $this->pkgVersion = $pkgVersion;
    }

    /**
     * @return mixed
     */
    public function getPackageVersionUpdateAvailable()
    {
        return $this->pkgAvailableVersion;
    }

    /**
     * @param mixed $pkgAvailableVersion
     */
    public function setPackageAvailableVersion($pkgAvailableVersion)
    {
        $this->pkgAvailableVersion = $pkgAvailableVersion;
    }

    /**
     * @return mixed
     */
    public function getPackageDescription()
    {
        return $this->pkgDescription;
    }

    /**
     * @param mixed $pkgDescription
     */
    public function setPackageDescription($pkgDescription)
    {
        $this->pkgDescription = $pkgDescription;
    }

    /**
     * @return mixed
     */
    public function getPackageDateInstalled()
    {
        return $this->pkgDateInstalled;
    }

    /**
     * @param mixed $pkgDateInstalled
     */
    public function setPackageDateInstalled($pkgDateInstalled)
    {
        $this->pkgDateInstalled = $pkgDateInstalled;
    }

    /**
     * @return mixed
     */
    public function getPackageName()
    {
        return $this->pkgName;
    }

    /**
     * @param mixed $pkgName
     */
    public function setPackageName($pkgName)
    {
        $this->pkgName = $pkgName;
    }

    public function __construct()
    {
        $this->pkgDateInstalled = new \DateTime();
    }

    public function getController()
    {
        return \Core::make('Concrete\Core\Package\PackageService')->getClass(
            $this->getPackageHandle()
        );
    }

    public function __call($method, $arguments)
    {
        $controller = $this->getController();
        return call_user_func_array(array($controller, $method), $arguments);
    }

    public function getTranslationFile($locale)
    {
        $controller = $this->getController();
        return $controller->getTranslationFile($locale);
    }

}
