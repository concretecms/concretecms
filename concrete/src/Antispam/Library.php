<?php
namespace Concrete\Core\Antispam;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Support\Facade\Facade;
use Loader;
use Package;
use Concrete\Core\Package\PackageList;
use Core;

class Library extends Object
{
    /**
     * @var string
     */
    public $saslHandle;
    /**
     * @var string
     */
    public $saslName;
    /**
     * @var bool
     */
    public $saslIsActive;
    /**
     * @var int
     */
    public $pkgID;

    /**
     * @return string
     */
    public function getSystemAntispamLibraryHandle()
    {
        return $this->saslHandle;
    }

    /**
     * @return string
     */
    public function getSystemAntispamLibraryName()
    {
        return $this->saslName;
    }

    /**
     * @return bool
     */
    public function isSystemAntispamLibraryActive()
    {
        return $this->saslIsActive;
    }

    /**
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return bool
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @return Package
     */
    public function getPackageObject()
    {
        return Package::getByID($this->pkgID);
    }

    /**
     * @return Library
     */
    public static function getActive()
    {
        $db = Loader::db();
        $saslHandle = $db->GetOne('select saslHandle from SystemAntispamLibraries where saslIsActive = 1');
        if ($saslHandle) {
            return static::getByHandle($saslHandle);
        }
    }

    /**
     * @param string $saslHandle
     *
     * @return static
     */
    public static function getByHandle($saslHandle)
    {
        $db = Loader::db();
        $r = $db->GetRow('select saslHandle, saslIsActive, pkgID, saslName from SystemAntispamLibraries where saslHandle = ?', array($saslHandle));
        if (is_array($r) && $r['saslHandle']) {
            $sc = new static();
            $sc->setPropertiesFromArray($r);

            return $sc;
        }
    }

    /**
     * @param string $saslHandle
     * @param string $saslName
     * @param bool|\Package $pkg
     *
     * @return Library
     */
    public static function add($saslHandle, $saslName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into SystemAntispamLibraries (saslHandle, saslName, pkgID) values (?, ?, ?)', array($saslHandle, $saslName, $pkgID));

        return static::getByHandle($saslHandle);
    }

    /**
     * Delete a library.
     */
    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from SystemAntispamLibraries where saslHandle = ?', array($this->saslHandle));
    }

    /**
     * Activate an Antispam library.
     */
    public function activate()
    {
        $db = Loader::db();
        self::deactivateAll();
        $db->Execute('update SystemAntispamLibraries set saslIsActive = 1 where saslHandle = ?', array($this->saslHandle));
    }

    /**
     * Deactivate all Antispam Libraries, (called by activate()).
     */
    public static function deactivateAll()
    {
        $db = Loader::db();
        $db->Execute('update SystemAntispamLibraries set saslIsActive = 0');
    }

    /**
     * @return Library[]
     */
    public static function getList()
    {
        $db = Loader::db();
        $saslHandles = $db->GetCol('select saslHandle from SystemAntispamLibraries order by saslHandle asc');
        $libraries = array();
        foreach ($saslHandles as $saslHandle) {
            $sasl = static::getByHandle($saslHandle);
            $libraries[] = $sasl;
        }

        return $libraries;
    }

    /**
     * @param \Package $pkg
     *
     * @return Library[]
     */
    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $saslHandles = $db->GetCol('select saslHandle from SystemAntispamLibraries where pkgID = ? order by saslHandle asc', array($pkg->getPackageID()));
        $libraries = array();
        foreach ($saslHandles as $saslHandle) {
            $sasl = static::getByHandle($saslHandle);
            $libraries[] = $sasl;
        }

        return $libraries;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('systemantispam');

        foreach ($list as $sc) {
            $type = $nxml->addChild('library');
            $type->addAttribute('handle', $sc->getSystemAntispamLibraryHandle());
            $type->addAttribute('name', $sc->getSystemAntispamLibraryName());
            $type->addAttribute('package', $sc->getPackageHandle());
            $type->addAttribute('activated', $sc->isSystemAntispamLibraryActive());
        }
    }

    /**
     * @return bool
     */
    public function hasOptionsForm()
    {
        $path = DIRNAME_SYSTEM . '/' . DIRNAME_SYSTEM_ANTISPAM . '/' . $this->saslHandle . '/' . FILENAME_FORM;
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

        return false;
    }

    /**
     * Returns the controller class for the currently selected captcha library.
     *
     * @return mixed
     */
    public function getController()
    {

        $class = overrideable_core_class('Core\\Antispam\\'
            . camelcase($this->saslHandle) . 'Controller', DIRNAME_CLASSES . '/Antispam/'
            . camelcase($this->saslHandle) . 'Controller.php',
            $this->getPackageHandle()
        );
        $app = Facade::getFacadeApplication();
        $cl = $app->make($class);
        return $cl;
    }
}
