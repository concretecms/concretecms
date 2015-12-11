<?php

namespace Concrete\Core\Attribute\Key;

use Database;
use CacheLocal;
use Concrete\Core\Attribute\Value\ValueList as AttributeValueList;
use Concrete\Core\Attribute\Value\CollectionValue as CollectionAttributeValue;

class CollectionKey extends Key
{
    public static function getDefaultIndexedSearchTable()
    {
        return 'CollectionSearchIndexAttributes';
    }

    protected $searchIndexFieldDefinition = array(
        'columns' => array(
            array('name' => 'cID', 'type' => 'integer', 'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)),
        ),
        'primary' => array('cID'),
    );

    /**
     * Returns an attribute value list of attributes and values (duh) which a collection version can store
     * against its object.
     *
     * @return AttributeValueList
     */
    public static function getAttributes($cID, $cvID, $method = 'getValue')
    {
        $db = Database::connection();
        $values = $db->fetchAll("select akID, avID from CollectionAttributeValues where cID = ? and cvID = ?", array($cID, $cvID));
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

    public static function getColumnHeaderList()
    {
        return parent::getAttributeKeyList('collection', array('akIsColumnHeader' => 1));
    }

    public static function getSearchableIndexedList()
    {
        return parent::getAttributeKeyList('collection', array('akIsSearchableIndexed' => 1));
    }

    public static function getSearchableList()
    {
        return parent::getAttributeKeyList('collection', array('akIsSearchable' => 1));
    }

    public function getAttributeValue($avID, $method = 'getValue')
    {
        $av = CollectionAttributeValue::getByID($avID);
        if (is_object($av)) {
            $av->setAttributeKey($this);
            $value = $av->{$method}();
            $av->__destruct();
            unset($av);

            return $value;
        }
    }

    public static function getByID($akID)
    {
        $ak = new static();
        $ak->load($akID);
        if ($ak->getAttributeKeyID() > 0) {
            return $ak;
        }
    }

    public static function getByHandle($akHandle)
    {
        $ak = CacheLocal::getEntry('collection_attribute_key_by_handle', $akHandle);
        if (is_object($ak)) {
            return $ak;
        } elseif ($ak == -1) {
            return false;
        }

        $ak = new static();
        $ak->load($akHandle, 'akHandle');
        if ($ak->getAttributeKeyID() < 1) {
            $ak = -1;
        }

        CacheLocal::set('collection_attribute_key_by_handle', $akHandle, $ak);

        if ($ak === -1) {
            return false;
        }

        return $ak;
    }

    public static function getList()
    {
        return parent::getAttributeKeyList('collection');
    }

    protected function saveAttribute($nvc, $value = false)
    {
        // We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
        // is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
        // otherwise generate new IDs
        $av = $nvc->getAttributeValueObject($this, true);
        parent::saveAttribute($av, $value);
        $db = Database::connection();
        $v = array($nvc->getCollectionID(), $nvc->getVersionID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
        $db->Replace('CollectionAttributeValues', array(
            'cID' => $nvc->getCollectionID(),
            'cvID' => $nvc->getVersionID(),
            'akID' => $this->getAttributeKeyID(),
            'avID' => $av->getAttributeValueID(),
        ), array('cID', 'cvID', 'akID'));
        unset($av);
    }

    public static function add($at, $args, $pkg = false)
    {

        // legacy check
        $fargs = func_get_args();
        if (count($fargs) >= 5) {
            $at = $fargs[4];
            $pkg = false;
            $args = array('akHandle' => $fargs[0], 'akName' => $fargs[1], 'akIsSearchable' => $fargs[2]);
        }

        CacheLocal::delete('collection_attribute_key_by_handle', $args['akHandle']);

        $ak = parent::addAttributeKey('collection', $at, $args, $pkg);

        return $ak;
    }

    public function delete()
    {
        parent::delete();
        $db = Database::connection();
        $r = $db->executeQuery('select avID from CollectionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->executeQuery('delete from AttributeValues where avID = ?', array($row['avID']));
        }
        $db->executeQuery('delete from CollectionAttributeValues where akID = ?', array($this->getAttributeKeyID()));
    }
}
