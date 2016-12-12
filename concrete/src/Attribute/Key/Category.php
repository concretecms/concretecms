<?php

namespace Concrete\Core\Attribute\Key;

use Core;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Attribute\Set as AttributeSet;
use Concrete\Core\Package\PackageList;
use Database;

class Category extends Object
{
    protected $akCategoryID;
    protected $akCategoryHandle;
    protected $akCategoryAllowSets;
    protected $pkgID;

    const ASET_ALLOW_NONE = 0;
    const ASET_ALLOW_SINGLE = 1;
    const ASET_ALLOW_MULTIPLE = 2;

    /**
     * @return int The attribute category unique numeric identifier
     */
    public function getAttributeKeyCategoryID()
    {
        return $this->akCategoryID;
    }

    /**
     * @return string The attribute category unique string identifier
     */
    public function getAttributeKeyCategoryHandle()
    {
        return $this->akCategoryHandle;
    }

    /**
     * @return int This is the integer corresponding to the attribute Category::ASET_ALLOW_* constant
     */
    public function allowAttributeSets()
    {
        return $this->akCategoryAllowSets;
    }

    /**
     * @return int|null Returns the integer of the category package, null if it does not belong to a package
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return string Returns the category package's unique string identifier if it belongs to a package, or false if
     * no package exists for this category
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @param int $akCategoryID
     *
     * @return self|null Returns an AttributeKeyCategory object for the given ID or null if no category exists with that ID
     */
    public static function getByID($akCategoryID)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc(
            'SELECT akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID
            FROM AttributeKeyCategories
            WHERE akCategoryID = ?',
            array($akCategoryID)
        );
        if (isset($row['akCategoryID'])) {
            /** @var self $akc */
            $akc = new static();
            $akc->setPropertiesFromArray($row);

            return $akc;
        }

        return null;
    }

    /**
     * @param string $akCategoryHandle
     *
     * @return self|null Returns an AttributeKeyCategory object for the given category handle or null if no category exists with that handle
     */
    public static function getByHandle($akCategoryHandle)
    {
        $db = Database::connection();
        $row = $db->fetchAssoc(
            'SELECT akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID
             FROM AttributeKeyCategories
             WHERE akCategoryHandle = ?',
            array($akCategoryHandle)
        );
        if (isset($row['akCategoryID'])) {
            /** @var self $akc */
            $akc = new static();
            $akc->setPropertiesFromArray($row);

            return $akc;
        }

        return null;
    }

    /**
     * @param string $akHandle
     *
     * @return bool Returns true if the handle already is in use for the category, false if is not yet in use
     */
    public function handleExists($akHandle)
    {
        $db = Database::connection();

        return $db->fetchColumn(
            'select count(akID) from AttributeKeys where akHandle = ? and akCategoryID = ?',
            array($akHandle, $this->akCategoryID)
        ) > 0;
    }

    /**
     * This function appends a list of attribute categories to the supplied SimpleXMLElement node.
     *
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $attribs = self::getList();
        $axml = $xml->addChild('attributecategories');
        foreach ($attribs as $akc) {
            $acat = $axml->addChild('category');
            $acat->addAttribute('handle', $akc->getAttributeKeyCategoryHandle());
            $acat->addAttribute('allow-sets', $akc->allowAttributeSets());
            $acat->addAttribute('package', $akc->getPackageHandle());
        }
    }

    /**
     * @param string $akHandle
     *
     * @return Key|false Returns an attribute key for the matching handle,
     * false if no key exists for the category with the given handle
     */
    public function getAttributeKeyByHandle($akHandle)
    {
        $txt = Core::make('helper/text');
        $prefix = ($this->pkgID > 0) ? PackageList::getHandle($this->pkgID) : false;
        $akCategoryHandle = $txt->camelcase($this->akCategoryHandle);
        $className = core_class('Core\\Attribute\\Key\\' . $akCategoryHandle . 'Key', $prefix);
        $ak = call_user_func(
            array(
                $className,
                'getByHandle',
            ),
            $akHandle
        );

        return $ak;
    }

    /**
     * @param int $akID
     *
     * @return Key|false Returns an attribute key for the matching ID,
     * false if no key exists for the category with the given ID
     */
    public function getAttributeKeyByID($akID)
    {
        $txt = Core::make('helper/text');
        $prefix = ($this->pkgID > 0) ? PackageList::getHandle($this->pkgID) : false;
        $akCategoryHandle = $txt->camelcase($this->akCategoryHandle);
        $className = core_class('Core\\Attribute\\Key\\' . $akCategoryHandle . 'Key', $prefix);
        $ak = call_user_func(
            array(
                $className,
                'getByID',
            ),
            $akID
        );

        return $ak;
    }

    /**
     * @return Key[] Returns an array of attribute keys for the current category that are not part of an Attribute Set
     */
    public function getUnassignedAttributeKeys()
    {
        $keys = array();
        $cat = static::getByID($this->akCategoryID);

        $unassignedAttributeKeys = Database::connection()->fetchAll(
            'SELECT AttributeKeys.akID
            FROM AttributeKeys
                LEFT JOIN AttributeSetKeys
                    ON AttributeKeys.akID = AttributeSetKeys.akID
            WHERE asID IS NULL AND akIsInternal = 0 AND akCategoryID = ?',
            array($this->akCategoryID)
        );
        foreach ($unassignedAttributeKeys as $row) {
            $keys[] = $cat->getAttributeKeyByID($row['akID']);
        }

        return $keys;
    }

    /**
     * @param \Package $pkg A Concrete5 Package object
     *
     * @return array
     */
    public static function getListByPackage($pkg)
    {
        $list = array();
        $categories = Database::connection()->fetchAll(
            'SELECT akCategoryID
            FROM AttributeKeyCategories
            WHERE pkgID = ?
            ORDER BY akCategoryID ASC',
            array($pkg->getPackageID())
        );
        foreach ($categories as $cat) {
            $list[] = static::getByID($cat['akCategoryID']);
        }

        return $list;
    }

    /**
     * This function will set the setting which determines if the category allows for sets or not.
     *
     * @param int $val This value should be one of the Category::ASET_ALLOW_* constants
     */
    public function setAllowAttributeSets($val)
    {
        Database::connection()->executeQuery(
            'UPDATE AttributeKeyCategories
            SET akCategoryAllowSets = ?
            WHERE akCategoryID = ?',
            array($val, $this->akCategoryID)
        );
        $this->akCategoryAllowSets = $val;
    }

    /**
     * @return AttributeSet[] Returns an array of attribute sets for the current Attribute Category
     */
    public function getAttributeSets()
    {
        $sets = Database::connection()->fetchAll(
            'SELECT asID
            FROM AttributeSets
            WHERE akCategoryID = ?
            ORDER BY asDisplayOrder ASC, asID ASC',
            array($this->akCategoryID)
        );
        $attributeSets = array();
        foreach ($sets as $set) {
            $attributeSets[] = AttributeSet::getByID($set['asID']);
        }

        return $attributeSets;
    }

    /**
     * @param string $asHandle
     *
     * @return AttributeSet Returns an AttributeSet object for the current category or null if no set exists with that handle
     */
    public function getAttributeSetByHandle($asHandle)
    {
        $attributeSet = AttributeSet::getByHandle($asHandle, $this->akCategoryID);

        return $attributeSet;
    }

    /**
     * Sets the Attribute Key Column Headers to false for all Attribute Keys in the category.
     */
    public function clearAttributeKeyCategoryColumnHeaders()
    {
        Database::connection()->executeQuery(
            'UPDATE AttributeKeys
            SET akIsColumnHeader = 0
            WHERE akCategoryID = ?',
            array($this->akCategoryID)
        );
    }

    /**
     * Associates the given attribute type with the current attribute category.
     *
     * @param \Concrete\Core\Attribute\Type $at
     */
    public function associateAttributeKeyType($at)
    {
        if (!$this->hasAttributeKeyTypeAssociated($at)) {
            Database::connection()->executeQuery(
                'INSERT INTO AttributeTypeCategories (atID, akCategoryID) VALUES (?, ?)',
                array($at->getAttributeTypeID(), $this->akCategoryID)
            );
        }
    }

    /**
     * @param \Concrete\Core\Attribute\Type $at An attribute type object
     *
     * @return bool True if the attribute type is associated with the current attribute category, false if not
     */
    public function hasAttributeKeyTypeAssociated($at)
    {
        $atCount = Database::connection()->fetchColumn(
            'SELECT COUNT(atID)
            FROM AttributeTypeCategories
            WHERE atID = ? AND akCategoryID = ?',
            array($at->getAttributeTypeID(), $this->akCategoryID)
        );

        return $atCount > 0;
    }

    /**
     * Removes all associated attribute types from the current category.
     */
    public function clearAttributeKeyCategoryTypes()
    {
        Database::connection()->executeQuery(
                'DELETE FROM AttributeTypeCategories WHERE akCategoryID = ?',
                array($this->akCategoryID)
            );
    }

    /**
     * Removes the attribute category and the association records for category types. Additionally, this will
     * unset any Category Column Headers from attribute keys where these were set for this category and will rescan
     * the set display order.
     *
     * This function will not remove attribute types or keys, only the associations to these.
     */
    public function delete()
    {
        $this->clearAttributeKeyCategoryTypes();
        $this->clearAttributeKeyCategoryColumnHeaders();
        $this->rescanSetDisplayOrder();
        Database::connection()->executeQuery(
            'DELETE FROM AttributeKeyCategories WHERE akCategoryID = ?',
            array($this->akCategoryID)
        );
    }

    /**
     * @return self[] Returns an array of category objects or an empty array if no category objects exist
     */
    public static function getList()
    {
        $cats = array();
        $categoryIDs = Database::connection()->fetchAll(
            'SELECT akCategoryID
            FROM AttributeKeyCategories
            ORDER BY akCategoryID ASC');
        foreach ($categoryIDs as $catID) {
            $cats[] = static::getByID($catID['akCategoryID']);
        }

        return $cats;
    }

    /**
     * @param string $akCategoryHandle The handle string for the category
     * @param int $akCategoryAllowSets This should be an attribute Category::ASET_ALLOW_* constant
     * @param bool|\Package $pkg The package object that the category belongs to, false if it does not belong to a package
     *
     * @return self|null Returns the category object if it was added successfully, or null if it failed to be added
     */
    public static function add($akCategoryHandle, $akCategoryAllowSets = 0, $pkg = false)
    {
        $pkgID = null;
        if (is_object($pkg)) {
            $pkgID = $pkg->getPackageID();
        }
        $db = Database::connection();
        $db->executeQuery(
            'INSERT INTO AttributeKeyCategories (akCategoryHandle, akCategoryAllowSets, pkgID) values (?, ?, ?)',
            array($akCategoryHandle, $akCategoryAllowSets, $pkgID)
        );
        $id = $db->lastInsertId();
        $txt = Core::make('helper/text');
        $prefix = ($pkgID > 0) ? $pkg->getPackageHandle() : false;
        $class = core_class('Core\\Attribute\\Key\\' . $txt->camelcase($akCategoryHandle) . 'Key', $prefix);
        /** @var \Concrete\Core\Attribute\Key\Key $obj This is really a specific category key object*/
        $obj = new $class();
        $obj->createIndexedSearchTable();

        return static::getByID($id);
    }

    /**
     * @param string $asHandle The unique attribute set handle
     * @param string $asName The attribute set name
     * @param bool|\Package $pkg The package object to associate the set with or false if it does not belong to a package
     * @param int $asIsLocked
     *
     * @return null|AttributeSet Returns the AttribueSet object if it was created successfully, null if it could not be
     * created (usually due to the category not allowing sets)
     */
    public function addSet($asHandle, $asName, $pkg = false, $asIsLocked = 1)
    {
        if ($this->akCategoryAllowSets > static::ASET_ALLOW_NONE) {
            $pkgID = 0;
            if (is_object($pkg)) {
                $pkgID = $pkg->getPackageID();
            }
            $db = Database::connection();
            $sets = $db->fetchColumn(
                'SELECT COUNT(asID) FROM AttributeSets WHERE akCategoryID = ?',
                array($this->akCategoryID)
            );

            $asDisplayOrder = 0;
            if ($sets > 0) {
                $asDisplayOrder = $db->fetchColumn(
                    'SELECt MAX(asDisplayOrder) FROM AttributeSets WHERE akCategoryID = ?',
                    array($this->akCategoryID)
                );
                ++$asDisplayOrder;
            }

            $db->executeQuery(
                'INSERT INTO AttributeSets
                (asHandle, asName, akCategoryID, asIsLocked, asDisplayOrder, pkgID)
                VALUES (?, ?, ?, ?, ?,?)',
                array($asHandle, $asName, $this->akCategoryID, $asIsLocked, $asDisplayOrder, $pkgID)
            );
            $id = $db->lastInsertId();

            $as = AttributeSet::getByID($id);

            return $as;
        }

        return null;
    }

    /**
     * This function rescans all attribute sets and assigns display order ID's based on their current display order
     * and set ID so that all display order ID's are unique and sequential starting at 0.
     */
    protected function rescanSetDisplayOrder()
    {
        $db = Database::connection();
        $categoryAttributeSetIDs = $db->fetchAll(
            'SELECT asID
            FROM AttributeSets
            WHERE akCategoryID = ?
            ORDER BY asDisplayOrder ASC, asID ASC',
            array($this->getAttributeKeyCategoryID())
        );
        $displayOrder = 0;
        foreach ($categoryAttributeSetIDs as $setID) {
            $db->executeQuery(
                'UPDATE AttributeSetKeys SET displayOrder = ? WHERE asID = ?',
                array($displayOrder, $setID['asID'])
            );
            ++$displayOrder;
        }
    }

    /**
     * This takes in array of attribute set ID's and reorders those ID's starting from 0 based on the order of the
     * array provided.
     *
     * @param array $asIDs An array of Attribute Set ID's to be re-ordered starting at
     */
    public function updateAttributeSetDisplayOrder($asIDs)
    {
        $db = Database::connection();
        for ($i = 0; $i < count($asIDs); ++$i) {
            $db->executeQuery(
                "UPDATE AttributeSets SET asDisplayOrder = {$i} WHERE akCategoryID = ? AND asID = ?",
                array($this->getAttributeKeyCategoryID(), $asIDs[$i])
            );
        }
    }
}
