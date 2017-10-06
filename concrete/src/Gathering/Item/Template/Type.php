<?php
namespace Concrete\Core\Gathering\Item\Template;

use Concrete\Core\Foundation\ConcreteObject;
use Loader;
use Concrete\Core\Package\PackageList;
use CacheLocal;

class Type extends ConcreteObject
{
    public function getGatheringItemTemplateTypeID()
    {
        return $this->gatTypeID;
    }
    public function getGatheringItemTemplateTypeHandle()
    {
        return $this->gatTypeHandle;
    }
    public function getGatheringItemTemplateTypeName()
    {
        return Loader::helper('text')->unhandle($this->gatTypeHandle);
    }

    public static function getByID($gatTypeID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select gatTypeID, pkgID, gatTypeHandle from GatheringItemTemplateTypes where gatTypeID = ?', array($gatTypeID));
        if ($row['gatTypeID']) {
            $wt = new static();
            $wt->setPropertiesFromArray($row);

            return $wt;
        }
    }

    public static function getList()
    {
        $gaItemTemplateTypeList = CacheLocal::getEntry('gaItemTemplateTypeList', false);
        if ($gaItemTemplateTypeList != false) {
            return $gaItemTemplateTypeList;
        }

        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select gatTypeID from GatheringItemTemplateTypes order by gatTypeID asc');

        while ($row = $r->FetchRow()) {
            $type = static::getByID($row['gatTypeID']);
            if (is_object($type)) {
                $list[] = $type;
            }
        }

        $r->Close();
        CacheLocal::set('gaItemTemplateTypeList', false, $list);

        return $list;
    }

    public static function exportList($xml)
    {
        $agtypes = static::getList();
        $db = Loader::db();
        $axml = $xml->addChild('gatheringitemtemplatetypes');
        foreach ($agtypes as $agt) {
            $atype = $axml->addChild('gatheringitemtemplatetype');
            $atype->addAttribute('handle', $agt->getGatheringItemTemplateTypeHandle());
            $atype->addAttribute('package', $agt->getPackageHandle());
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute("delete from GatheringItemTemplateTypes where gatTypeID = ?", array($this->gatTypeID));
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select gatTypeID from GatheringItemTemplateTypes where pkgID = ? order by gatTypeID asc', array($pkg->getPackageID()));
        while ($row = $r->FetchRow()) {
            $type = static::getByID($row['gatTypeID']);
            if (is_object($type)) {
                $list[] = $type;
            }
        }
        $r->Close();

        return $list;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    public static function getByHandle($gatTypeHandle)
    {
        $db = Loader::db();
        $gatTypeID = $db->GetOne('select gatTypeID from GatheringItemTemplateTypes where gatTypeHandle = ?', array($gatTypeHandle));
        if ($gatTypeID > 0) {
            return self::getByID($gatTypeID);
        }
    }

    public static function add($gatTypeHandle, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into GatheringItemTemplateTypes (gatTypeHandle, pkgID) values (?, ?)', array($gatTypeHandle, $pkgID));
        $id = $db->Insert_ID();
        $est = static::getByID($id);

        return $est;
    }
}
