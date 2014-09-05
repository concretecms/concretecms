<?php
namespace Concrete\Core\Attribute\Value;
use \Concrete\Core\Foundation\Object;
use Loader;
class Value extends Object {

	protected $attributeType;

	public static function getByID($avID) {
		$av = new static();
		$av->load($avID);
		if ($av->getAttributeValueID() == $avID) {
			return $av;
		}
	}

	protected function load($avID) {
		$db = Loader::db();
		$row = $db->GetRow("select avID, akID, uID, avDateAdded, atID from AttributeValues where avID = ?", array($avID));
		if (is_array($row) && $row['avID'] == $avID) {
			$this->setPropertiesFromArray($row);
		}

		$this->attributeType = $this->getAttributeTypeObject();
		$this->attributeType->controller->setAttributeKey($this->getAttributeKey());
		$this->attributeType->controller->setAttributeValue($this);
	}

	public function __destruct() {
		if (is_object($this->attributeType)) {
			$this->attributeType->__destruct();
			unset($this->attributeType);
		}
	}

	public function getValue($mode = false) {
		if ($mode != false) {
			$th = Loader::helper('text');
			$modes = func_get_args();
			foreach($modes as $mode) {
				$method = 'get' . $th->camelcase($mode) . 'Value';
				if (method_exists($this->attributeType->controller, $method)) {
					return $this->attributeType->controller->{$method}();
				}
			}
		}
		return $this->attributeType->controller->getValue();
	}

	public function getSearchIndexValue() {
		if (method_exists($this->attributeType->controller, 'getSearchIndexValue')) {
			return $this->attributeType->controller->getSearchIndexValue();
		} else {
			return $this->attributeType->controller->getValue();
		}
	}

	public function delete() {
		$this->attributeType->controller->deleteValue();
		$db = Loader::db();
		$db->Execute('delete from AttributeValues where avID = ?', $this->getAttributeValueID());
	}

	public function getAttributeKey() {
		return $this->attributeKey;
	}
	public function setAttributeKey($ak) {
		$this->attributeKey = $ak;
		$this->attributeType->controller->setAttributeKey($ak);
	}
	public function getAttributeValueID() { return $this->avID;}
	public function getAttributeValueUserID() { return $this->uID;}
	public function getAttributeValueDateAdded() { return $this->avDateAdded;}
	public function getAttributeTypeID() { return $this->atID;}
	public function getAttributeTypeObject() {
		$ato = \Concrete\Core\Attribute\Type::getByID($this->atID);
		return $ato;
	}

}
