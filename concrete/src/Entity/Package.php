<?php

namespace Concrete\Core\Entity;

use Concrete\Core\Package\LocalizablePackageInterface;
use Concrete\Core\Package\PackageService;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\Application;

/**
 * @ORM\Entity
 * @ORM\Table(name="Packages")
 */
class Package implements LocalizablePackageInterface
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $pkgID;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $pkgHandle;

    /**
     * @ORM\Column(type="string")
     */
    protected $pkgVersion;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $pkgIsInstalled = true;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pkgAvailableVersion;

    /**
     * @ORM\Column(type="text")
     */
    protected $pkgDescription;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $pkgDateInstalled;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pkgName;

    /**
     * @return string
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * @param string $pkgHandle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    /**
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @param int $pkgID
     */
    public function setPackageID($pkgID)
    {
        $this->pkgID = $pkgID;
    }

    /**
     * @return bool
     */
    public function isPackageInstalled()
    {
        return $this->pkgIsInstalled;
    }

    /**
     * @param bool $pkgIsInstalled
     */
    public function setIsPackageInstalled($pkgIsInstalled)
    {
        $this->pkgIsInstalled = $pkgIsInstalled;
    }

    /**
     * @return string
     */
    public function getPackageVersion()
    {
        return $this->pkgVersion;
    }

    /**
     * @param string $pkgVersion
     */
    public function setPackageVersion($pkgVersion)
    {
        $this->pkgVersion = $pkgVersion;
    }

    /**
     * @return string|null
     */
    public function getPackageVersionUpdateAvailable()
    {
        return $this->pkgAvailableVersion;
    }

    /**
     * @param string|null $pkgAvailableVersion
     */
    public function setPackageAvailableVersion($pkgAvailableVersion)
    {
        $this->pkgAvailableVersion = $pkgAvailableVersion;
    }

    /**
     * @return string
     */
    public function getPackageDescription()
    {
        return $this->pkgDescription;
    }

    /**
     * @param string $pkgDescription
     */
    public function setPackageDescription($pkgDescription)
    {
        $this->pkgDescription = $pkgDescription;
    }

    /**
     * @return DateTime
     */
    public function getPackageDateInstalled()
    {
        return $this->pkgDateInstalled;
    }

    /**
     * @param DateTime $pkgDateInstalled
     */
    public function setPackageDateInstalled($pkgDateInstalled)
    {
        $this->pkgDateInstalled = $pkgDateInstalled;
    }

    /**
     * @return string|null
     */
    public function getPackageName()
    {
        return $this->pkgName;
    }

    /**
     * @param string|null $pkgName
     */
    public function setPackageName($pkgName)
    {
        $this->pkgName = $pkgName;
    }

    public function __construct()
    {
        $this->pkgDateInstalled = new DateTime();
    }

    public function getController()
    {
        $app = Application::getFacadeApplication();

        return $app->make(PackageService::class)->getClass($this->getPackageHandle());
    }

    public function __call($method, $arguments)
    {
        $controller = $this->getController();

        return call_user_func_array(array($controller, $method), $arguments);
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getTranslationFile($locale)
    {
        $controller = $this->getController();

        return $controller->getTranslationFile($locale);
    }
}
