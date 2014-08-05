<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Database\Driver\PDOStatement;
use Concrete\Core\Package\Package;
use Loader;

class ControlSet
{

    public $scsID;
    public $scsHandle;
    public $scsName;
    public $scsDisplayOrder;
    public $pkgID;

    /**
     * Retrieves a list of control set objects.
     *
     * @return ControlSet[]
     */
    public static function getList()
    {
        $db = Loader::db();
        $q = $db->query('SELECT * FROM SystemImageEditorControlSets');
        $cs = self::getSortedListFromQuery($q);
        return $cs;
    }

    /**
     * Fancy way to sort using the display order
     * Not super useful right now.
     *
     * This method naturally sorts first by display order, then by the orderby
     * on the query.
     *
     * @param PDOStatement $q
     * @return ControlSet[]
     */
    public static function getSortedListFromQuery($q)
    {
        $unsorted = array();
        while ($row = $q->FetchRow()) {
            $cs = self::load($row);
            $oid = $cs->getImageEditorControlSetDisplayOrder();
            if (!$unsorted[$oid]) {
                $unsorted[$oid] = array();
            }
            $unsorted[$oid][] = $cs;
        }
        $sorted = array();
        foreach ($unsorted as $arr) {
            foreach ($arr as $v) {
                $sorted[] = $v;
            }
        }
        return $sorted;
    }

    public static function load($arr)
    {
        $obj = new static;
        foreach ((array)$arr as $key => $val) {
            $obj->{$key} = $val;
        }
        return $obj;
    }

    public function getImageEditorControlSetDisplayOrder()
    {
        return $this->scsDisplayOrder;
    }

    /**
     * Retrieves a list of control set objects by package
     * this is used in package uninstall.
     *
     * @param Package $pkg
     * @return ControlSet[]
     */
    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorControlSets WHERE pkgID=?',
                array($pkg->getPackageID()));
        $cs = self::getSortedListFromQuery($q);
        return $cs;
    }

    /**
     * Get the basic object
     */
    public static function getByID($scsID)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorControlSets
                                            WHERE scsID=?',
                array($scsID));
        return self::load($q->FetchRow());
    }

    /**
     * Basic management of these objects
     */
    public static function add($scsHandle, $scsName, $pkg = false)
    {
        $db = Loader::db();
        $pkgID = (is_object($pkg)) ? $pkg->getPackageID() : 0;
        $db->execute(
           'INSERT INTO SystemImageEditorControlSets
                                   (scsHandle,scsName,pkgID) VALUES (?,?,?)',
           array($scsHandle, $scsName, $pkgID));
        return self::getByHandle($scsHandle);
    }

    public static function getByHandle($scsHandle)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorControlSets
                                            WHERE scsHandle=?',
                array($scsHandle));
        return self::load($q->FetchRow());
    }

    /**
     * Retrieve Data
     */
    public function getID()
    {
        return $this->scsID;
    }

    public function getHandle()
    {
        return $this->scsHandle;
    }

    public function getName()
    {
        return $this->scsName;
    }

    /** Returns the display name for this instance (localized and escaped accordingly to $format)
     *
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('ImageEditorControlSetName', $this->scsName);
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function getPackageHandle()
    {
        return $this->getPackageObject()->getPackageHandle();
    }

    public function getPackageObject()
    {
        return Package::getByID($this->getPackageID());
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    public function delete()
    {
        $db = Loader::db();
        $db->execute(
           'DELETE FROM SystemImageEditorControlSets WHERE scsID=?',
           array($this->scsID));
        return true;
    }

    public function getAssetPath($directory, $extension, $full_path)
    {
        $file = $this->getHandle() . $extension;

        if ($this->pkgID) {
            $package = $this->getPackageObject();
            if ($package && is_object($package) && !$package->isError()) {
                $path = $package->getPackagePath() . '/' . $directory . '/image-editor/control-sets/' . $file;
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        $path = '/' . $directory . '/image-editor/control-sets/' . $file;
        if (file_exists(DIR_BASE . $path)) {
            return ($full_path ? DIR_BASE : DIR_REL) . $path;
        }
        return ($full_path ? DIR_BASE : DIR_REL) . '/concrete' . $path;
    }

    public function getViewPath()
    {
        return $this->getAssetPath('views', '.php', true);
    }

    public function getJavascriptPath()
    {
        return $this->getAssetPath('js', '.js', false);
    }

    public function getCssPath()
    {
        return $this->getAssetPath('css', '.css', false);
    }

}
