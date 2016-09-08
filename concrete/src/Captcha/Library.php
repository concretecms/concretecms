<?php
namespace Concrete\Core\Captcha;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Package as PackageService;
use Concrete\Core\Support\Facade\Facade;

class Library extends Object
{
    /**
     * The library handle.
     *
     * @var string
     */
    public $sclHandle;

    /**
     * The library name.
     *
     * @var string
     */
    public $sclName;

    /**
     * Is this the active library?
     *
     * @var bool
     */
    public $sclIsActive;

    public $pkgHandle;

    /**
     * The package ID (or 0 if it's a core library).
     *
     * @var int
     */
    protected $pkgID;

    /**
     * Get the library handle.
     *
     * @return string
     */
    public function getSystemCaptchaLibraryHandle()
    {
        return $this->sclHandle;
    }

    /**
     * Get the library name.
     *
     * @return string
     */
    public function getSystemCaptchaLibraryName()
    {
        return $this->sclName;
    }

    /**
     * Is this the active library?
     *
     * @return bool
     */
    public function isSystemCaptchaLibraryActive()
    {
        return $this->sclIsActive;
    }

    /**
     * Get the package ID (or 0 if it's a core library).
     *
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the package handle (or false if it's a core library).
     *
     * @return string|false
     */
    public function getPackageHandle()
    {
        if (!isset($this->pkgHandle)) {
            $this->pkgHandle = $this->pkgID ? PackageList::getHandle($this->pkgID) : false;
        }
        return $this->pkgHandle;
    }

    /**
     * Get the package instance (or null if it's a core library).
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public function getPackageObject()
    {
        return $this->pkgID ? PackageService::getByID($this->pkgID) : null;
    }

    /**
     * Get the active library.
     *
     * @return static|null
     */
    public static function getActive()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $sclHandle = $db->fetchColumn('select sclHandle from SystemCaptchaLibraries where sclIsActive = 1');

        return ($sclHandle === false) ? null : static::getByHandle($sclHandle);
    }

    /**
     * Get a library given its handle.
     *
     * @param string $sclHandle The library handle.
     *
     * @return static|null
     */
    public static function getByHandle($sclHandle)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $r = $db->fetchAssoc('select sclHandle, sclIsActive, pkgID, sclName from SystemCaptchaLibraries where sclHandle = ?', array($sclHandle));
        if ($r !== false) {
            $sc = new static();
            $sc->setPropertiesFromArray($r);

            return $sc;
        }
    }

    /**
     * Add a new library.
     *
     * @param string $sclHandle The library handle.
     * @param string $sclName The library name.
     * @param \Concrete\Core\Entity\Package|int|false $pkg The package that installs this library.
     *
     * @return static
     */
    public static function add($sclHandle, $sclName, $pkg = false)
    {
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        } else {
            $pkgID = (int) $pkg;
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('insert into SystemCaptchaLibraries (sclHandle, sclName, pkgID) values (?, ?, ?)', array($sclHandle, $sclName, $pkgID));

        return static::getByHandle($sclHandle);
    }

    /**
     * Delete this library (if it's the default one we'll activate the default core library).
     */
    public function delete()
    {
        $active = static::getActive();
        if ($active !== null && $active->getSystemCaptchaLibraryHandle() === $this->sclHandle) {
            if ($scl = static::getByHandle('securimage')) {
                $scl->activate();
            }
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('delete from SystemCaptchaLibraries where sclHandle = ?', array($this->sclHandle));
    }

    /**
     * Make this library the active one.
     */
    public function activate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('update SystemCaptchaLibraries set sclIsActive = 0');
        $db->executeQuery('update SystemCaptchaLibraries set sclIsActive = 1 where sclHandle = ?', array($this->sclHandle));
        $this->sclIsActive = 1;
    }

    /**
     * Retrieve all the installed libraries.
     *
     * @return static[]
     */
    public static function getList()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $libraries = array();
        foreach ($db->fetchAll('select sclHandle from SystemCaptchaLibraries order by sclHandle asc') as $row) {
            $scl = static::getByHandle($row['sclHandle']);
            $libraries[] = $scl;
        }

        return $libraries;
    }

    /**
     * Retrieve the libraries installed by a package.
     *
     * @param \Concrete\Core\Entity\Package|int $pkg The package instance (or its ID).
     *
     * @return static[]
     */
    public static function getListByPackage($pkg)
    {
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        } else {
            $pkgID = (int) $pkg;
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $libraries = array();
        foreach ($db->fetchAll('select sclHandle from SystemCaptchaLibraries where pkgID = ? order by sclHandle asc', array($pkgID)) as $row) {
            $scl = static::getByHandle($row['sclHandle']);
            $libraries[] = $scl;
        }

        return $libraries;
    }

    /**
     * Export the data of this library.
     *
     * @param \SimpleXMLElement $xml The parent XML element.
     */
    public function export(\SimpleXMLElement $xml)
    {
        $type = $xml->addChild('library');
        $type->addAttribute('handle', $this->getSystemCaptchaLibraryHandle());
        $type->addAttribute('name', $this->getSystemCaptchaLibraryName());
        $type->addAttribute('package', $this->getPackageHandle());
        $type->addAttribute('activated', $this->isSystemCaptchaLibraryActive() ? '1' : '0');
    }

    /**
     * Export the data of all the libraries.
     *
     * @param \SimpleXMLElement $xml The parent XML element.
     */
    public static function exportList(\SimpleXMLElement $xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('systemcaptcha');

        foreach ($list as $sc) {
            $sc->export($nxml);
        }
    }

    /**
     * Does this library has an option form?
     *
     * @return bool
     */
    public function hasOptionsForm()
    {
        $path = DIRNAME_SYSTEM . '/' . DIRNAME_SYSTEM_CAPTCHA . '/' . $this->sclHandle . '/' . FILENAME_FORM;
        if (file_exists(DIR_FILES_ELEMENTS . '/' . $path)) {
            return true;
        } elseif ($this->pkgID > 0) {
            $pkgHandle = $this->getPackageHandle();
            $dp = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . $path;
            $dpc = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_ELEMENTS . '/' . $path;
            if (file_exists($dp)) {
                return true;
            } elseif (file_exists($dpc)) {
                return true;
            }
        } else {
            return file_exists(DIR_FILES_ELEMENTS_CORE . '/' . $path);
        }
    }

    /**
     * Returns the controller class for the currently selected captcha library.
     *
     * @return \Concrete\Core\Captcha\Controller
     */
    public function getController()
    {
        $class = overrideable_core_class('Core\\Captcha\\'
            . camelcase($this->sclHandle) . 'Controller', DIRNAME_CLASSES . '/Captcha/'
            . camelcase($this->sclHandle) . 'Controller.php',
            $this->getPackageHandle()
        );
        $app = Facade::getFacadeApplication();
        $cl = $app->make($class);

        return $cl;
    }
}
