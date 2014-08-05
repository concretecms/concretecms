<?php
namespace Concrete\Core\ImageEditor;

use Concrete\Core\Package\Package;
use Loader;

class Filter
{

    public $scsID;
    public $scsHandle;
    public $scsName;
    public $scsDisplayOrder;
    public $pkgID;

    /**
     * Retrieves a list of filter objects.
     * Retrieves a list of filter objects.
     *
     * @return Filter[]
     */
    public static function getList()
    {
        $db = Loader::db();
        $q = $db->query('SELECT * FROM SystemImageEditorFilters');
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
     * @param $q
     * @return Filter[]
     */
    public static function getSortedListFromQuery($q)
    {
        $unsorted = array();
        while ($row = $q->FetchRow()) {
            $cs = self::load($row);
            $oid = $cs->getDisplayOrder();
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

    /**
     * @param $arr
     * @return static
     */
    public static function load($arr)
    {
        $obj = new static;
        foreach ((array)$arr as $key => $val) {
            $obj->{$key} = $val;
        }
        return $obj;
    }

    /**
     * Retrieves a list of filter objects by package
     * this is used in package uninstall.
     *
     * @param Package $pkg
     * @return Filter[]
     */
    public static function getListByPackage($pkg)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorFilters
                                            WHERE pkgID=?',
                array($pkg->getPackageID()));
        $cs = self::getSortedListFromQuery($q);
        return $cs;
    }

    /**
     * Get the basic object
     *
     * @param $scsID
     * @return Filter
     */
    public static function getByID($scsID)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorFilters
                                            WHERE scsID=?',
                array($scsID));
        return self::load($q->FetchRow());
    }

    /**
     * Basic management of these objects
     *
     * @param      $scsHandle
     * @param      $scsName
     * @param bool $pkg
     * @return Filter
     */
    public static function add($scsHandle, $scsName, $pkg = false)
    {
        $db = Loader::db();
        $pkgID = (is_object($pkg)) ? $pkg->getPackageID() : 0;
        $db->execute(
           'INSERT INTO SystemImageEditorFilters
                                   (scsHandle,scsName,pkgID) VALUES (?,?,?)',
           array($scsHandle, $scsName, $pkgID));
        return self::getByHandle($scsHandle);
    }

    /**
     * @param $scsHandle
     * @return Filter
     */
    public static function getByHandle($scsHandle)
    {
        $db = Loader::db();
        $q = $db->query(
                'SELECT * FROM SystemImageEditorFilters
                                            WHERE scsHandle=?',
                array($scsHandle));
        return self::load($q->FetchRow());
    }

    /**
     * @return int
     */
    public function getDisplayOrder()
    {
        return intval($this->scsDisplayOrder, 10);
    }

    /**
     * @return int
     */
    public function getID()
    {
        return intval($this->scsID, 10);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->scsName;
    }

    /**
     * Returns the display name for this instance (localized and escaped accordingly to $format)
     *
     * @param string $format Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('ImageEditorFilterName', $this->scsName);
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * @return string
     */
    public function getPackageHandle()
    {
        return $this->getPackageObject()->getPackageHandle();
    }

    /**
     * @return Package
     */
    public function getPackageObject()
    {
        return Package::getByID($this->getPackageID());
    }

    /**
     * @return int
     */
    public function getPackageID()
    {
        return intval($this->pkgID, 10);
    }

    /**
     * Delete this filter
     *
     * @return bool
     */
    public function delete()
    {
        $db = Loader::db();
        $db->execute(
           'DELETE FROM SystemImageEditorFilters WHERE scsID=?',
           array($this->scsID));
        return true;
    }

    /**
     * Get the handle of this filter
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->scsHandle;
    }

    /**
     * Get the path to an asset
     *
     * @param string $directory The directory (views|js|css)
     * @param string $extension The extension (.php|.js|.css)
     * @param bool   $full_path Should we return the full path?
     * @return string
     */
    public function getAssetPath($directory, $extension, $full_path = true)
    {
        $file = $this->getHandle() . $extension;

        if ($this->pkgID) {
            $package = $this->getPackageObject();
            if ($package && is_object($package) && !$package->isError()) {
                $path = $package->getPackagePath() . '/' . $directory . '/image-editor/filters/' . $file;
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        $path = '/' . $directory . '/image-editor/filters/' . $file;
        if (file_exists(DIR_BASE . $path)) {
            return ($full_path ? DIR_BASE : DIR_REL) . $path;
        }
        return ($full_path ? DIR_BASE : DIR_REL) . '/concrete' . $path;
    }

    /**
     * Get the full path to the view
     *
     * @return string
     */
    public function getViewPath()
    {
        return $this->getAssetPath('views', '.php', true);
    }

    /**
     * Get the JavaScript path
     *
     * @return string
     */
    public function getJavascriptPath()
    {
        return $this->getAssetPath('js', '.js', false);
    }

    /**
     * Get the CSS path
     *
     * @return string
     */
    public function getCssPath()
    {
        return $this->getAssetPath('css', '.css', false);
    }

}
