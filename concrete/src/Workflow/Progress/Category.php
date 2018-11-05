<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Foundation\ConcreteObject;
use Loader;
use Concrete\Core\Package\PackageList;

class Category extends ConcreteObject
{
    public static function getByID($wpCategoryID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select wpCategoryID, wpCategoryHandle, pkgID from WorkflowProgressCategories where wpCategoryID = ?', array($wpCategoryID));
        if (isset($row['wpCategoryID'])) {
            $pkc = new static();
            $pkc->setPropertiesFromArray($row);

            return $pkc;
        }
    }

    public static function getByHandle($wpCategoryHandle)
    {
        $db = Loader::db();
        $row = $db->GetRow('select wpCategoryID, wpCategoryHandle, pkgID from WorkflowProgressCategories where wpCategoryHandle = ?', array($wpCategoryHandle));
        if (isset($row['wpCategoryID'])) {
            $pkc = new static();
            $pkc->setPropertiesFromArray($row);

            return $pkc;
        }
    }

    public static function exportList($xml)
    {
        $attribs = static::getList();
        $axml = $xml->addChild('workflowprogresscategories');
        foreach ($attribs as $pkc) {
            $acat = $axml->addChild('category');
            $acat->addAttribute('handle', $pkc->getWorkflowProgressCategoryHandle());
            $acat->addAttribute('package', $pkc->getPackageHandle());
        }
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select wpCategoryID from WorkflowProgressCategories where pkgID = ? order by wpCategoryID asc', array($pkg->getPackageID()));
        while ($row = $r->FetchRow()) {
            $list[] = static::getByID($row['wpCategoryID']);
        }
        $r->Close();

        return $list;
    }

    public function getWorkflowProgressCategoryID()
    {
        return $this->wpCategoryID;
    }

    public function getWorkflowProgressCategoryHandle()
    {
        return $this->wpCategoryHandle;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    public function getWorkflowProgressCategoryClass()
    {
        $className = '\\Core\\Workflow\\Progress\\' . Loader::helper('text')->camelcase($this->wpCategoryHandle) . 'Progress';
        $pkHandle = $this->getPackageHandle();
        $class = core_class($className, $pkHandle);
        if (class_exists($class)) {
            $c = new $class();
            return $c;
        }
    }

    public function __call($method, $arguments)
    {
        $class = $this->getWorkflowProgressCategoryClass();
        if ($class) {
            return call_user_func_array(array($class, $method), $arguments);
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from WorkflowProgressCategories where wpCategoryID = ?', array($this->wpCategoryID));
    }

    public static function getList()
    {
        $db = Loader::db();
        $cats = array();
        $r = $db->Execute('select wpCategoryID from WorkflowProgressCategories order by wpCategoryID asc');
        while ($row = $r->FetchRow()) {
            $cats[] = static::getByID($row['wpCategoryID']);
        }

        return $cats;
    }

    public static function add($wpCategoryHandle, $pkg = false)
    {
        $db = Loader::db();
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        } else {
            $pkgID = $pkg ?: null;
        }
        $db->Execute('insert into WorkflowProgressCategories (wpCategoryHandle, pkgID) values (?, ?)', array($wpCategoryHandle, $pkgID));
        $id = $db->Insert_ID();

        return static::getByID($id);
    }
}
