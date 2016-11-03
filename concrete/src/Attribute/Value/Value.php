<?php


namespace Concrete\Core\Attribute\Value;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Foundation\Object;
use Loader;

/*
 * @deprecated
 */
class Value extends Object implements AttributeValueInterface
{
    protected $attributeType;
    protected $attributeKey;

    public function getController()
    {
        $controller = $this->attributeKey->getController();
        $controller->setAttributeValue($this);
        return $controller;
    }

    public function getValueObject()
    {
        // First, retrieve the corresponding LegacyAttributeValue for this
        // object
        $orm = \Database::connection()->getEntityManager();
        $r = $orm->getRepository('Concrete\Core\Entity\Attribute\Value\LegacyValue');
        $value = $r->findOneBy(['avID' => $this->getAttributeValueID()]);
        if (is_object($value)) {
            return $value->getValueObject();
        }
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Value\Value\Value $value
     */
    public function setValue(\Concrete\Core\Entity\Attribute\Value\Value\Value $value)
    {
        $orm = \Database::connection()->getEntityManager();
        $r = $orm->getRepository('Concrete\Core\Entity\Attribute\Value\LegacyValue');
        $attributeValue = $r->findOneBy(['avID' => $this->getAttributeValueID()]);
        if (is_object($attributeValue)) {
            $attributeValue->setValue($value);
        }
        $orm->persist($attributeValue);
        $orm->flush();
    }

    public static function getByID($avID)
    {
        $av = new static();
        $av->load($avID);
        if ($av->getAttributeValueID() == $avID) {
            return $av;
        }
    }

    protected function load($avID)
    {
        $db = Loader::db();
//        $row = $db->GetRow('select avID, akID, uID, avDateAdded, atID from AttributeValues where avID = ?', array($avID));
        $row = $db->GetRow('select avID, akID from AttributeValues where avID = ?', array($avID));
        if (is_array($row) && $row['avID'] == $avID) {
            $this->setPropertiesFromArray($row);
            $this->attributeKey = Key::getByID($row['akID']);
            $this->attributeType = $this->getAttributeTypeObject();
        }
    }

    public function setAttributeKey($ak)
    {
        $this->attributeKey = $ak;
    }

    /**
     * Validates the current attribute value to see if it fulfills the "requirement" portion of an attribute.
     * @return bool|\Concrete\Core\Error\Error
     */
    public function validateAttributeValue()
    {
        $at = $this->attributeType;
        $at->getController()->setAttributeKey($this->attributeKey);
        $e = true;
        if (method_exists($at->getController(), 'validateValue')) {
            $e = $at->getController()->validateValue();
        }
        return $e;
    }

    public function getValue($mode = false)
    {
        $value = $this->getValueObject();
        $controller = $this->getController();
        if (is_object($value)) {
            if ($mode != false) {
                $modes = func_get_args();
                foreach ($modes as $mode) {
                    $method = 'get' . camelcase($mode) . 'Value';
                    if (method_exists($controller, $method)) {
                        return $controller->{$method}();
                    }
                }
            } else {
                return $value->getValue();
            }
        }

        return $controller->getValue();
    }

    public function getSearchIndexValue()
    {
        if (method_exists($this->attributeType->getController(), 'getSearchIndexValue')) {
            return $this->getController()->getSearchIndexValue();
        } else {
            return $this->getController()->getValue();
        }
    }

    public function delete()
    {
        $this->getController()->deleteValue();
        $db = Loader::db();
        $db->Execute('delete from AttributeValues where avID = ?', $this->getAttributeValueID());
    }

    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    public function getAttributeValueID()
    {
        return $this->avID;
    }
    public function getAttributeValueUserID()
    {
        return $this->uID;
    }
    public function getAttributeValueDateAdded()
    {
        return $this->avDateAdded;
    }
    public function getAttributeTypeID()
    {
        return $this->atID;
    }
    public function getAttributeTypeObject()
    {
        $ato = \Concrete\Core\Attribute\Type::getByHandle($this->getAttributeKey()->getAttributeTypeHandle());

        return $ato;
    }
}
