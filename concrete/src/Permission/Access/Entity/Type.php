<?php
namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Permission\Category;
use Concrete\Core\Support\Facade\Facade;
use Gettext\Translations;
use Loader;
use Core;
use Concrete\Core\Package\PackageList;

class Type extends ConcreteObject
{
    public function getAccessEntityTypeID()
    {
        return $this->petID;
    }

    public function getAccessEntityTypeHandle()
    {
        return $this->petHandle;
    }

    public function getAccessEntityTypeName()
    {
        return $this->petName;
    }

    /**
     * Returns the controller class for the currently selected captcha library.
     */
    public function getAccessEntityTypeClass()
    {
        $app = Facade::getFacadeApplication();
        $entity = $app->make('permission/access/entity/factory')->createEntity($this);
        return get_class($entity);
    }

    /** Returns the display name for this access entity type (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getAccessEntityTypeDisplayName($format = 'html')
    {
        $value = tc('PermissionAccessEntityTypeName', $this->getAccessEntityTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public static function getByID($petID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select petID, pkgID, petHandle, petName from PermissionAccessEntityTypes where petID = ?',
            array($petID));
        if (isset($row['petHandle']) && $row['petHandle']) {
            $wt = new static();
            $wt->setPropertiesFromArray($row);

            return $wt;
        }
    }

    public function __call($method, $args)
    {
        $obj = Core::make($this->getAccessEntityTypeClass());

        return call_user_func_array(array($obj, $method), $args);
    }

    public static function getList($category = false)
    {
        $db = Loader::db();
        $list = array();
        if ($category instanceof Category) {
            $r = $db->Execute('select pet.petID from PermissionAccessEntityTypes pet inner join PermissionAccessEntityTypeCategories petc on pet.petID = petc.petID where petc.pkCategoryID = ? order by pet.petID asc',
                array($category->getPermissionKeyCategoryID()));
        } else {
            $r = $db->Execute('select petID from PermissionAccessEntityTypes order by petID asc');
        }

        while ($row = $r->fetch()) {
            $list[] = static::getByID($row['petID']);
        }

        $r->free();

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

    public static function exportList($xml)
    {
        $ptypes = static::getList();
        $db = Loader::db();
        $axml = $xml->addChild('permissionaccessentitytypes');
        foreach ($ptypes as $pt) {
            $ptype = $axml->addChild('permissionaccessentitytype');
            $ptype->addAttribute('handle', $pt->getAccessEntityTypeHandle());
            $ptype->addAttribute('name', $pt->getAccessEntityTypeName());
            $ptype->addAttribute('package', $pt->getPackageHandle());
            $categories = $db->GetCol('select pkCategoryHandle from PermissionKeyCategories inner join PermissionAccessEntityTypeCategories where PermissionKeyCategories.pkCategoryID = PermissionAccessEntityTypeCategories.pkCategoryID and PermissionAccessEntityTypeCategories.petID = ?',
                array($pt->getAccessEntityTypeID()));
            if (count($categories) > 0) {
                $cat = $ptype->addChild('categories');
                foreach ($categories as $catHandle) {
                    $cat->addChild('category')->addAttribute('handle', $catHandle);
                }
            }
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute("delete from PermissionAccessEntityTypes where petID = ?", array($this->petID));
    }

    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $list = array();
        $r = $db->Execute('select petID from PermissionAccessEntityTypes where pkgID = ? order by petID asc',
            array($pkg->getPackageID()));
        while ($row = $r->fetch()) {
            $list[] = static::getByID($row['petID']);
        }
        $r->free();

        return $list;
    }

    public static function getByHandle($petHandle)
    {
        $db = Loader::db();
        $petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = ?', array($petHandle));
        if ($petID > 0) {
            return self::getByID($petID);
        }
    }

    public static function add($petHandle, $petName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Loader::db();
        $db->Execute('insert into PermissionAccessEntityTypes (petHandle, petName, pkgID) values (?, ?, ?)',
            array($petHandle, $petName, $pkgID));
        $id = $db->Insert_ID();
        $est = static::getByID($id);

        return $est;
    }

    public static function exportTranslations()
    {
        $translations = new Translations();
        $attribs = static::getList();
        foreach ($attribs as $type) {
            $translations->insert('PermissionAccessEntityTypeName', $type->getAccessEntityTypeName());
        }

        return $translations;
    }

    public function getControllerUrl($action = 'get_or_create')
    {
        $app = Facade::getFacadeApplication();
        $url = $app->make('url/manager')->resolve([
            '/ccm/system/permissions/access/entity/types',
            $this->petHandle,
            $action
        ]);
        $token = $app->make('token');
        $url .= '?' . $token->getParameter($action);
        return $url;
    }
}
