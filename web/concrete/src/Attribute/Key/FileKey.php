<?php

namespace Concrete\Core\Attribute\Key;

use Database;
use CacheLocal;
use Concrete\Core\Attribute\Value\ValueList as AttributeValueList;
use Concrete\Core\Attribute\Value\FileValue as FileAttributeValue;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Attribute\Type as AttributeType;
use File;
use FileVersion;

class FileKey extends Key
{
    public static function getDefaultIndexedSearchTable()
    {
        return 'FileSearchIndexAttributes';
    }

    protected $searchIndexFieldDefinition = array(
        'columns' => array(
            array('name' => 'fID', 'type' => 'integer', 'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)),
        ),
        'primary' => array('fID'),
    );

    /**
     * Returns an attribute value list of attributes and values (duh) which a collection version can store
     * against its object.
     *
     * @return AttributeValueList
     */
    public static function getAttributes($fID, $fvID, $method = 'getValue')
    {
        $db = Database::connection();
        $values = $db->fetchAll("select akID, avID from FileAttributeValues where fID = ? and fvID = ?", array($fID, $fvID));
        $avl = new AttributeValueList();
        foreach ($values as $val) {
            $ak = static::getByID($val['akID']);
            if (is_object($ak)) {
                $value = $ak->getAttributeValue($val['avID'], $method);
                $avl->addAttributeValue($ak, $value);
            }
        }

        return $avl;
    }

    public function getAttributeValue($avID, $method = 'getValue')
    {
        $av = FileAttributeValue::getByID($avID);
        if (is_object($av)) {
            $av->setAttributeKey($this);

            return $av->{$method}();
        }
    }

    public static function getByHandle($akHandle)
    {
        $ak = CacheLocal::getEntry('file_attribute_key_by_handle', $akHandle);
        if (is_object($ak)) {
            return $ak;
        } elseif ($ak == -1) {
            return false;
        }

        $ak = -1;
        $db = Database::connection();
        $q = "SELECT ak.akID FROM AttributeKeys ak INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID  WHERE ak.akHandle = ? AND akc.akCategoryHandle = 'file'";
        $akID = $db->fetchColumn($q, array($akHandle));
        if ($akID > 0) {
            $ak = self::getByID($akID);
        } else {
            // else we check to see if it's listed in the initial registry
             $ia = FileTypeList::getImporterAttribute($akHandle);
            if (is_object($ia)) {
                // we create this attribute and return it.
                $at = AttributeType::getByHandle($ia->akType);
                $args = array(
                    'akHandle' => $akHandle,
                    'akName' => $ia->akName,
                    'akIsSearchable' => 1,
                    'akIsAutoCreated' => 1,
                    'akIsEditable' => $ia->akIsEditable,
                );
                $ak = static::add($at, $args);
            }
        }
        CacheLocal::set('file_attribute_key_by_handle', $akHandle, $ak);
        if ($ak === -1) {
            return false;
        }

        return $ak;
    }

    public static function getByID($akID)
    {
        $ak = new static();
        $ak->load($akID);
        if ($ak->getAttributeKeyID() > 0) {
            return $ak;
        }
    }

    public static function getList()
    {
        return parent::getAttributeKeyList('file');
    }

    public static function getSearchableList()
    {
        return parent::getAttributeKeyList('file', array('akIsSearchable' => 1));
    }

    public static function getSearchableIndexedList()
    {
        return parent::getAttributeKeyList('file', array('akIsSearchableIndexed' => 1));
    }

    public static function getImporterList($fv = false)
    {
        $list = parent::getAttributeKeyList('file', array('akIsAutoCreated' => 1));
        if ($fv == false) {
            return $list;
        }
        $list2 = array();
        $db = Database::connection();
        foreach ($list as $l) {
            $r = $db->fetchColumn('select count(akID) from FileAttributeValues where fID = ? and fvID = ? and akID = ?', array($fv->getFileID(), $fv->getFileVersionID(), $l->getAttributeKeyID()));
            if ($r > 0) {
                $list2[] = $l;
            }
        }

        return $list2;
    }

    public static function getUserAddedList()
    {
        return parent::getAttributeKeyList('file', array('akIsAutoCreated' => 0));
    }

    /**
     */
    public static function get($akID)
    {
        return static::getByID($akID);
    }

    protected function saveAttribute($f, $value = false)
    {
        // We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
        // is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
        // otherwise generate new IDs
        $av = $f->getAttributeValueObject($this, true);
        parent::saveAttribute($av, $value);
        $db = Database::connection();
        $db->Replace('FileAttributeValues', array(
            'fID' => $f->getFileID(),
            'fvID' => $f->getFileVersionID(),
            'akID' => $this->getAttributeKeyID(),
            'avID' => $av->getAttributeValueID(),
        ), array('fID', 'fvID', 'akID'));
        $f->logVersionUpdate(FileVersion::UT_EXTENDED_ATTRIBUTE, $this->getAttributeKeyID());
        $fo = $f->getFile();
        $fo->reindex();
        unset($av);
        unset($fo);
        unset($f);
    }

    public static function add($at, $args, $pkg = false)
    {
        CacheLocal::delete('file_attribute_key_by_handle', $args['akHandle']);
        $ak = parent::addAttributeKey('file', $at, $args, $pkg);

        return $ak;
    }

    public static function getColumnHeaderList()
    {
        return parent::getAttributeKeyList('file', array('akIsColumnHeader' => 1));
    }

    public function delete()
    {
        parent::delete();
        $db = Database::connection();
        $r = $db->executeQuery('select avID from FileAttributeValues where akID = ?', array($this->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->executeQuery('delete from AttributeValues where avID = ?', array($row['avID']));
        }
        $db->executeQuery('delete from FileAttributeValues where akID = ?', array($this->getAttributeKeyID()));
    }
}
