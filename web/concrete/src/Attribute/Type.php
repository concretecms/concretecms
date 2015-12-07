<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Foundation\Object;
use Concrete\Core\Attribute\View as AttributeTypeView;
use Gettext\Translations;
use Database;
use Concrete\Core\Package\PackageList;
use Environment;
use Package;
use Core;

/**
 * Base class for attribute types.
 *
 * @method static Type[] getList(string|false $akCategoryHandle) Deprecated method. Use Key::getAttributeTypeList instead.
 */
class Type extends Object
{
    /** @var  \Concrete\Core\Attribute\Controller */
    public $controller;
    public $atName;
    public $atHandle;

    protected $atID;
    protected $pkgID;

    public function getAttributeTypeID()
    {
        return $this->atID;
    }

    public function getAttributeTypeHandle()
    {
        return $this->atHandle;
    }

    public function getAttributeTypeName()
    {
        return $this->atName;
    }

    public function getController()
    {
        return $this->controller;
    }

    /** Returns the display name for this attribute type (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getAttributeTypeDisplayName($format = 'html')
    {
        $value = tc('AttributeTypeName', $this->getAttributeTypeName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public static function getByID($atID)
    {
        $db = Database::get();
        $row = $db->GetRow('select atID, pkgID, atHandle, atName from AttributeTypes where atID = ?', array($atID));
        $at = new static();
        $at->setPropertiesFromArray($row);
        $at->loadController();

        return $at;
    }

    public function __destruct()
    {
        unset($this->controller);
    }

    public static function __callStatic($name, $arguments)
    {
        if (strcasecmp($name, 'getList') === 0) {
            return call_user_func_array('static::getAttributeTypeList', $arguments);
        }
        trigger_error("Call to undefined method ".__CLASS__."::$name()", E_USER_ERROR);
    }

    public static function getAttributeTypeList($akCategoryHandle = false)
    {
        $db = Database::get();
        $list = array();
        if ($akCategoryHandle == false) {
            $r = $db->Execute('select atID from AttributeTypes order by atID asc');
        } else {
            $r = $db->Execute(
                'select atID from AttributeTypeCategories inner join AttributeKeyCategories on AttributeTypeCategories.akCategoryID = AttributeKeyCategories.akCategoryID and AttributeKeyCategories.akCategoryHandle = ? order by atID asc',
                array($akCategoryHandle)
            );
        }

        while ($row = $r->FetchRow()) {
            $list[] = static::getByID($row['atID']);
        }
        $r->Close();

        return $list;
    }

    public function export($xml)
    {
        $db = Database::get();
        $atype = $xml->addChild('attributetype');
        $atype->addAttribute('handle', $this->getAttributeTypeHandle());
        $atype->addAttribute('package', $this->getPackageHandle());
        $categories = $db->GetCol(
            'select akCategoryHandle from AttributeKeyCategories inner join AttributeTypeCategories where AttributeKeyCategories.akCategoryID = AttributeTypeCategories.akCategoryID and AttributeTypeCategories.atID = ?',
            array($this->getAttributeTypeID())
        );
        if (count($categories) > 0) {
            $cat = $atype->addChild('categories');
            foreach ($categories as $catHandle) {
                $cat->addChild('category')->addAttribute('handle', $catHandle);
            }
        }
    }

    public static function exportList($xml)
    {
        $attribs = static::getAttributeTypeList();
        $db = Database::get();
        $axml = $xml->addChild('attributetypes');
        foreach ($attribs as $at) {
            $at->export($axml);
        }
    }

    public function delete()
    {
        $db = Database::get();
        if (method_exists($this->controller, 'deleteType')) {
            $this->controller->deleteType();
        }
        $db->Execute("delete from AttributeTypes where atID = ?", array($this->atID));
        $db->Execute("delete from AttributeTypeCategories where atID = ?", array($this->atID));
    }

    public static function getListByPackage($pkg)
    {
        $db = Database::get();
        $list = array();
        $r = $db->Execute(
            'select atID from AttributeTypes where pkgID = ? order by atID asc',
            array($pkg->getPackageID())
        );
        while ($row = $r->FetchRow()) {
            $list[] = static::getByID($row['atID']);
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

    public function isAssociatedWithCategory($cat)
    {
        $db = Database::get();
        $r = $db->GetOne(
            "select count(akCategoryID) from AttributeTypeCategories where akCategoryID = ? and atID = ?",
            array($cat->getAttributeKeyCategoryID(), $this->getAttributeTypeID())
        );

        return $r > 0;
    }

    public static function getByHandle($atHandle)
    {
        // Handle legacy handles
        switch ($atHandle) {
            case 'date':
                $atHandle = 'date_time';
                break;
        }

        $db = Database::get();
        $row = $db->GetRow(
            'select atID, pkgID, atHandle, atName from AttributeTypes where atHandle = ?',
            array($atHandle)
        );
        if ($row && $row['atID']) {
            $at = new static();
            $at->setPropertiesFromArray($row);
            $at->loadController();

            return $at;
        }
    }

    public static function add($atHandle, $atName, $pkg = false)
    {
        $pkgID = 0;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Database::get();
        $db->Execute(
            'insert into AttributeTypes (atHandle, atName, pkgID) values (?, ?, ?)',
            array($atHandle, $atName, $pkgID)
        );
        $id = $db->Insert_ID();
        $est = static::getByID($id);

        $path = $est->getAttributeTypeFilePath(FILENAME_ATTRIBUTE_DB);
        if ($path) {
            Package::installDB($path);
        }

        return $est;
    }

    public function getValue($avID)
    {
        $cnt = $this->getController();

        return $cnt->getValue($avID);
    }

    public function render($view, $ak = false, $value = false, $return = false)
    {
        // local scope
        if ($value) {
            $av = new AttributeTypeView($value);
        } else {
            if ($ak) {
                $av = new AttributeTypeView($ak);
            } else {
                $av = new AttributeTypeView($this);
            }
        }
        ob_start();
        $av->render($view);
        $contents = ob_get_contents();
        ob_end_clean();
        if ($return) {
            return $contents;
        } else {
            print $contents;
        }
    }

    public function getAttributeTypeIconSRC()
    {
        $env = Environment::get();
        $url = $env->getURL(
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $this->getAttributeTypeHandle() . '/' . FILENAME_BLOCK_ICON)),
            $this->getPackageHandle()
        );

        return $url;
    }

    public function getAttributeTypeFilePath($_file)
    {
        $env = Environment::get();
        $r = $env->getRecord(
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $this->getAttributeTypeHandle() . '/' . $_file)),
            $this->getPackageHandle()
        );
        if ($r->exists()) {
            return $r->file;
        }
    }

    public function getAttributeTypeFileURL($_file)
    {
        $env = Environment::get();
        $r = $env->getRecord(
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $this->getAttributeTypeHandle() . '/' . $_file)),
            $this->getPackageHandle()
        );
        if ($r->exists()) {
            return $r->url;
        }
    }

    public function loadController()
    {
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_ATTRIBUTES . '/' . $this->atHandle . '/' . FILENAME_CONTROLLER);
        $prefix = $r->override ? true : $this->getPackageHandle();
        $atHandle = Core::make('helper/text')->camelcase($this->atHandle);
        $class = core_class('Attribute\\' . $atHandle . '\\Controller', $prefix);
        $this->controller = Core::make($class, array($this));
    }

    public static function exportTranslations()
    {
        $translations = new Translations();
        $attribs = static::getAttributeTypeList();
        foreach ($attribs as $type) {
            $translations->insert('AttributeTypeName', $type->getAttributeTypeName());
        }

        return $translations;
    }
}
