<?php
namespace Concrete\Core\Captcha;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Package;
use Concrete\Core\Support\Facade\Facade;

class Library extends Object
{
    public function getSystemCaptchaLibraryHandle()
    {
        return $this->sclHandle;
    }
    public function getSystemCaptchaLibraryName()
    {
        return $this->sclName;
    }
    public function isSystemCaptchaLibraryActive()
    {
        return $this->sclIsActive;
    }
    public function getPackageID()
    {
        return $this->pkgID;
    }
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }
    public function getPackageObject()
    {
        return Package::getByID($this->pkgID);
    }

    public static function getActive()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $sclHandle = $db->fetchColumn('select sclHandle from SystemCaptchaLibraries where sclIsActive = 1');

        return ($sclHandle === false) ? null : static::getByHandle($sclHandle);
    }

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

    public static function add($sclHandle, $sclName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('insert into SystemCaptchaLibraries (sclHandle, sclName, pkgID) values (?, ?, ?)', array($sclHandle, $sclName, $pkgID));

        return static::getByHandle($sclHandle);
    }

    public function delete()
    {
        if (static::getActive()->getSystemCaptchaLibraryHandle() == $this->sclHandle) {
            if ($scl = static::getByHandle('securimage')) {
                $scl->activate();
            }
        }
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('delete from SystemCaptchaLibraries where sclHandle = ?', array($this->sclHandle));
    }

    public function activate()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('update SystemCaptchaLibraries set sclIsActive = 0');
        $db->executeQuery('update SystemCaptchaLibraries set sclIsActive = 1 where sclHandle = ?', array($this->sclHandle));
    }

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

    public static function getListByPackage($pkg)
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $libraries = array();
        foreach ($db->fetchAll('select sclHandle from SystemCaptchaLibraries where pkgID = ? order by sclHandle asc', array($pkg->getPackageID())) as $row) {
            $scl = static::getByHandle($row['sclHandle']);
            $libraries[] = $scl;
        }

        return $libraries;
    }

    public function export($xml)
    {
        $type = $xml->addChild('library');
        $type->addAttribute('handle', $this->getSystemCaptchaLibraryHandle());
        $type->addAttribute('name', $this->getSystemCaptchaLibraryName());
        $type->addAttribute('package', $this->getPackageHandle());
        $type->addAttribute('activated', $this->isSystemCaptchaLibraryActive());
    }

    public static function exportList($xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('systemcaptcha');

        foreach ($list as $sc) {
            $sc->export($nxml);
        }
    }

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

        return false;
    }

    /**
     * Returns the controller class for the currently selected captcha library.
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
