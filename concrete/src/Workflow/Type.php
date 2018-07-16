<?php
namespace Concrete\Core\Workflow;

use Concrete\Core\Foundation\ConcreteObject;
use Loader;
use Concrete\Core\Package\PackageList;

class Type extends ConcreteObject
{
    public function getWorkflowTypeID()
    {
        return $this->wftID;
    }
    public function getWorkflowTypeHandle()
    {
        return $this->wftHandle;
    }
    public function getWorkflowTypeName()
    {
        return $this->wftName;
    }

    public static function getByID($wftID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select wftID, pkgID, wftHandle, wftName from WorkflowTypes where wftID = ?', array($wftID));
        if ($row['wftHandle']) {
            $wt = new static();
            $wt->setPropertiesFromArray($row);

            return $wt;
        }
    }

    public static function getList()
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select wftID from WorkflowTypes order by wftID asc');

        while ($row = $r->FetchRow()) {
            $list[] = static::getByID($row['wftID']);
        }

        $r->Close();

        return $list;
    }

    public static function exportList($xml)
    {
        $wtypes = static::getList();
        $db = Loader::db();
        $axml = $xml->addChild('workflowtypes');
        foreach ($wtypes as $wt) {
            $wtype = $axml->addChild('workflowtype');
            $wtype->addAttribute('handle', $wt->getWorkflowTypeHandle());
            $wtype->addAttribute('name', $wt->getWorkflowTypeName());
            $wtype->addAttribute('package', $wt->getPackageHandle());
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute("delete from WorkflowTypes where wftID = ?", array($this->wftID));
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select wftID from WorkflowTypes where pkgID = ? order by wftID asc', array($pkg->getPackageID()));
        while ($row = $r->FetchRow()) {
            $list[] = static::getByID($row['wftID']);
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

    public static function getByHandle($wftHandle)
    {
        $db = Loader::db();
        $wftID = $db->GetOne('select wftID from WorkflowTypes where wftHandle = ?', array($wftHandle));
        if ($wftID > 0) {
            return self::getByID($wftID);
        }
    }

    public static function add($wftHandle, $wftName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into WorkflowTypes (wftHandle, wftName, pkgID) values (?, ?, ?)', array($wftHandle, $wftName, $pkgID));
        $id = $db->Insert_ID();
        $est = static::getByID($id);

        return $est;
    }
}
