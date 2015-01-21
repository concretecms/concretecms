<?php
namespace Concrete\Core\Attribute\Value;

use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Calendar\Event\Event;

class EventValue extends Value
{

    /** @var Event */
    protected $event;

    public static function getAttributeValueObject(Event $event, EventKey $key, $create_on_miss = false)
    {
        $db = \Database::connection();

        $value = null;
        $avID = $db->GetOne(
            "SELECT avID FROM CalendarEventAttributeValues WHERE eventID = ? AND akID = ?",
            array(
                $event->getId(),
                $key->getAttributeKeyID()
            ));

        if ($avID > 0) {
            $value = static::getByID($avID);
            if (is_object($value)) {
                $value->setEvent($event);
                $value->setAttributeKey($key);
            }
        }

        if ($create_on_miss) {
            $count = 0;

            // Is this avID in use ?
            if (is_object($value)) {
                $count = $db->GetOne(
                    "SELECT count(avID) FROM CalendarEventAttributeValues WHERE avID = ?",
                    $value->getAttributeValueID()
                );
            }

            if ((!is_object($value)) || ($count > 1)) {
                $new_value = $key->addAttributeValue();

                $value = EventValue::getByID($new_value->getAttributeValueID());
                $value->setEvent($event);

                $db->insert(
                    'CalendarEventAttributeValues',
                    array(
                        'eventID'  => $event->getID(),
                        'akID' => $key->getKeyID(),
                        'avID' => $value->getAttributeValueID()
                    ));
            }
        }

        return $value;
    }

    /**
     * @param $value_id
     * @return null|static
     */
    public static function getByID($value_id)
    {
        $value_id = intval($value_id, 10);
        $value = new static();
        $value->load($value_id);
        if ($value->getAttributeValueID() == $value_id) {
            return $value;
        }

        return null;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function delete()
    {
        $db = \Database::connection();
        $db->Execute(
            'DELETE FROM CalendarEventAttributeValues WHERE eventID = ? AND akID = ? AND avID = ?',
            array(
                $this->u->getUserID(),
                $this->attributeKey->getAttributeKeyID(),
                $this->getAttributeValueID()
            ));

        $num = $db->GetOne(
            'SELECT count(avID) FROM CalendarEventAttributeValues WHERE avID = ?',
            array($this->getAttributeValueID()));
        if ($num < 1) {
            parent::delete();
        }

    }

}
