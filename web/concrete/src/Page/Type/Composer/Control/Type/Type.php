<?php
namespace Concrete\Core\Page\Type\Composer\Control\Type;

use Loader;
use Core;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;

abstract class Type extends Object
{
    abstract public function getPageTypeComposerControlObjects();
    abstract public function getPageTypeComposerControlByIdentifier($identifier);
    abstract public function configureFromImportHandle($handle);

    public function controlTypeSupportsOutputControl()
    {
        return false;
    }
    public function getPageTypeComposerControlTypeName()
    {
        return $this->ptComposerControlTypeName;
    }
    public function getPageTypeComposerControlTypeDisplayName($format = 'html')
    {
        $value = tc('PageTypeComposerControlTypeName', $this->getPageTypeComposerControlTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }
    public function getPageTypeComposerControlTypeHandle()
    {
        return $this->ptComposerControlTypeHandle;
    }
    public function getPageTypeComposerControlTypeID()
    {
        return $this->ptComposerControlTypeID;
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

    public static function getByHandle($ptComposerControlTypeHandle)
    {
        $db = Loader::db();
        $r = $db->GetRow('select ptComposerControlTypeID, ptComposerControlTypeHandle, ptComposerControlTypeName, pkgID from PageTypeComposerControlTypes where ptComposerControlTypeHandle = ?', array($ptComposerControlTypeHandle));
        if (is_array($r) && $r['ptComposerControlTypeHandle']) {
            $txt = Loader::helper('text');
            $class = '\\Concrete\\Core\\Page\\Type\\Composer\\Control\\Type\\' . $txt->camelcase($r['ptComposerControlTypeHandle']) . 'Type';
            $sc = Core::make($class);
            $sc->setPropertiesFromArray($r);

            return $sc;
        }
    }
    public static function getByID($ptComposerControlTypeID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select ptComposerControlTypeID, ptComposerControlTypeHandle, ptComposerControlTypeName, pkgID from PageTypeComposerControlTypes where ptComposerControlTypeID = ?', array($ptComposerControlTypeID));
        if (is_array($r) && $r['ptComposerControlTypeHandle']) {
            $txt = Loader::helper('text');
            $class = '\\Concrete\\Core\\Page\\Type\\Composer\\Control\\Type\\' . $txt->camelcase($r['ptComposerControlTypeHandle']) . 'Type';
            $sc = Core::make($class);
            $sc->setPropertiesFromArray($r);

            return $sc;
        }
    }

    public static function add($ptComposerControlTypeHandle, $ptComposerControlTypeName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into PageTypeComposerControlTypes (ptComposerControlTypeHandle, ptComposerControlTypeName, pkgID) values (?, ?, ?)', array($ptComposerControlTypeHandle, $ptComposerControlTypeName, $pkgID));

        return static::getByHandle($ptComposerControlTypeHandle);
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from PageTypeComposerControlTypes where ptComposerControlTypeID = ?', array($this->ptComposerControlTypeID));
    }

    public static function getList()
    {
        $db = Loader::db();
        $ids = $db->GetCol('select ptComposerControlTypeID from PageTypeComposerControlTypes order by ptComposerControlTypeName asc');
        $types = array();
        foreach ($ids as $id) {
            $type = static::getByID($id);
            if (is_object($type)) {
                $types[] = $type;
            }
        }

        return $types;
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $ids = $db->GetCol('select ptComposerControlTypeID from PageTypeComposerControlTypes where pkgID = ? order by ptComposerControlTypeName asc', array($pkg->getPackageID()));
        $types = array();
        foreach ($ids as $id) {
            $type = static::getByID($id);
            if (is_object($type)) {
                $types[] = $type;
            }
        }

        return $types;
    }

    public function export($xml)
    {
        $type = $xml->addChild('type');
        $type->addAttribute('handle', $this->getPageTypeComposerControlTypeHandle());
        $type->addAttribute('name', $this->getPageTypeComposerControlTypeName());
        $type->addAttribute('package', $this->getPackageHandle());
    }

    public static function exportList($xml)
    {
        $list = self::getList();
        $nxml = $xml->addChild('pagetypecomposercontroltypes');

        foreach ($list as $sc) {
            $sc->export($nxml);
        }
    }
}
