<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Value\EventValue;
use Concrete\Core\Attribute\Value\ValueList as AttributeValueList;
use Concrete\Core\Calendar\Event\Event;

class EventKey extends Key
{

    protected $searchIndexFieldDefinition = array(
        'columns' => array(
            array(
                'name'    => 'eventID',
                'type'    => 'integer',
                'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true))
        ),
        'primary' => array('eventID')
    );

    /**
     * Returns an attribute value list of attributes and values (duh) which a collection version can store
     * against its object.
     *
     * @return AttributeValueList
     */
    public static function getAttributes($eventID, $method = 'getValue')
    {
        $db = \Database::connection();
        $values = $db->GetAll(
            "SELECT akID, avID FROM CalendarEventAttributeValues WHERE eventID = ?",
            array($eventID));
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

    /**
     * @param $akID
     * @return EventKey
     */
    public static function getByID($akID)
    {
        $ak = new static();
        $ak->load($akID);
        if ($ak->getAttributeKeyID() > 0) {
            return $ak;
        }
    }

    public function getAttributeValue($avID, $method = 'getValue')
    {
        $av = EventValue::getByID($avID);
        if (is_object($av)) {
            $av->setAttributeKey($this);
            return $av->{$method}();
        }
    }

    public static function getByHandle($akHandle)
    {
        $ak = \CacheLocal::getEntry('event_attribute_key_by_handle', $akHandle);
        if (is_object($ak)) {
            return $ak;
        } else {
            if ($ak == -1) {
                return false;
            }
        }

        $ak = -1;
        $db = \Database::connection();
        $q = "SELECT ak.akID FROM AttributeKeys ak INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID  WHERE ak.akHandle = ? AND akc.akCategoryHandle = 'event'";
        $akID = $db->GetOne($q, array($akHandle));
        if ($akID > 0) {
            $ak = self::getByID($akID);
        }
        \CacheLocal::set('event_attribute_key_by_handle', $akHandle, $ak);
        if ($ak === -1) {
            return false;
        }
        return $ak;
    }

    public static function add($at, $args, $pkg = false)
    {
        \CacheLocal::delete('event_attribute_key_by_handle', $args['akHandle']);
        $ak = parent::add('event', $at, $args, $pkg);
        return $ak;
    }

    public static function getList()
    {
        return parent::getList('event');
    }

    public static function getSearchableList()
    {
        return parent::getList('event', array('akIsSearchable' => 1));
    }

    public static function getSearchableIndexedList()
    {
        return parent::getList('event', array('akIsSearchableIndexed' => 1));
    }

    public static function getImporterList(Event $event = null)
    {
        $list = parent::getList('event', array('akIsAutoCreated' => 1));
        if (!$event) {
            return $list;
        }
        $list2 = array();
        $db = \Database::connection();
        foreach ($list as $l) {
            $r = $db->GetOne(
                'SELECT count(akID) FROM CalendarEventAttributeValues WHERE eventID = ? AND akID = ?',
                array($event->getId(), $l->getAttributeKeyID()));
            if ($r > 0) {
                $list2[] = $l;
            }
        }
        return $list2;
    }

    public static function getUserAddedList()
    {
        return parent::getList('event', array('akIsAutoCreated' => 0));
    }

    /**
     * @access private
     */
    public static function get($akID)
    {
        return static::getByID($akID);
    }

    public static function getColumnHeaderList()
    {
        return parent::getList('event', array('akIsColumnHeader' => 1));
    }

    public function getIndexedSearchTable()
    {
        return 'CalendarEventSearchIndexAttributes';
    }

    public function delete()
    {
        parent::delete();
        $db = \Database::connection();
        $r = $db->Execute('SELECT avID FROM CalendarEventAttributeValues WHERE akID = ?', array($this->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->Execute('DELETE FROM AttributeValues WHERE avID = ?', array($row['avID']));
        }
        $db->Execute('DELETE FROM CalendarEventAttributeValues WHERE akID = ?', array($this->getAttributeKeyID()));
    }

    protected function saveAttribute(Event $event, $value = false)
    {
        // We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
        // is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
        // otherwise generate new IDs
        $av = $event->getAttributeValueObject($this, true);
        parent::saveAttribute($av, $value);
        $db = \Database::connection();
        $db->Replace(
            'CalendarEventAttributeValues',
            array(
                'eventID'  => $event->getID(),
                'akID' => $this->getAttributeKeyID(),
                'avID' => $av->getAttributeValueID()
            ),
            array('eventID', 'akID'));

        $event->reindex();
        unset($av);
        unset($event);
    }
}
