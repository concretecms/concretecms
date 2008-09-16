<?
	
	class Attribute extends Object {
	
		function Attribute($row = null) {
			if (is_array($row)) {
				foreach($row as $key=>$value) {
					foreach($row as $key => $value) {
						$this->{$key} = $value;
					}
				}
			}
		}
		
		// static get()
		function get($atID) {
			global $db;
			$v = array($atID);
			$q = "select uID, attribute, value, itemID, itemType, timestamp, metaDescription from Attributes where atID = ?";
			$r = $db->query($q, $v);
			if (!PEAR::isError($r)) {
				$row = $r->fetchRow();
				$at = new Attribute;
				$at->atID = $atID;
				foreach($row as $key => $value) {
					$at->{$key} = $value;
				}
				return $at;
			}
		}
		
		function getAttributeUserID() {return $this->uID;}
		function getAttributeName() {return $this->attribute;}
		function getAttributeValue() {return $this->value;}
		function getAttributeItemID() {return $this->itemID;}
		function getAttributeItemType() {return $this->itemType;}
		function getAttributeItemObject() {
			switch($this->itemType) {
					
			}		
		}
		function getAttributeTimestamp() {return $this->timestamp;}
		function getAttributeMetaDescription() {return $this->metaDescription;}
		
		function getUniqueAttribute($itemType, $itemID, $attributeFilter) {
			// This function is given a type, an ID, and an attribute type, and returns an attribute object, if applicable
			// Useful for when there may be one of a given attribute (areas) - like, "maxBlocks", etc...
			global $db;
			$v = array($itemType, $itemID, $attributeFilter);
			$q = "select atID from Attributes where itemType = ? and itemID = ? and attribute = ?";
			$atID = $db->getOne($q, $v);
			if ($atID > 0) {
				return Attribute::get($atID);
			}
		}
		
		function getItemAttributes($itemType, $itemID, $personObj = null, $attributeFilter = null) {
			global $db;
			$attribs = array();
			
			$q = "select atID, uID, attribute, value, itemID, itemType, timestamp, metaDescription from Attributes where itemType = '{$itemType}' and itemID = '{$itemID}' ";
			if (isset($personObj)) {
				switch(strtolower(get_class($personObj))) {
					case "user":
						$q .= "and uID = " . $personObj->getUserID() . " ";
						break;
				}
			}
			if (isset($attributeFilter)) {
				$q .= "and attribute = '{$attributeFilter}' ";
			}
			$q .= "order by timestamp asc";
			$r = $db->query($q);
			if (!PEAR::isError($r)) {
				while ($row = $r->fetchRow()) {
					$at = new Attribute($row);
					$attribs[] = $at;
				}
			}
			return $attribs;
		}
		
		function addItemAttribute($itemType, $itemID, $personObj, $attribute, $value, $metaDescription) {
			global $db;
			
			switch(strtolower(get_class($personObj))) {
				case "user":
					$v = array($itemType, $itemID, $personObj->getUserID(), $attribute, $value, $metaDescription);				
					break;
			}
			$q = "insert into Attributes (itemType, itemID, uID, attribute, value, metaDescription) values (?, ?, ?, ?, ?, ?)";			
			$r = $db->query($q, $v);
			if (!PEAR::isError($r)) {
				return true;
			}
		}
		
		function getAttributeAverage($itemType, $itemID, $attribute) {
			global $db;
			$q = "select avg(value) as avg from Attributes where itemType = '{$itemType}' and itemID = '{$itemID}' and attribute = '{$attribute}' group by itemID";
			$avg = $db->getOne($q);
			if (!PEAR::isError($avg)) {
				if ($avg != null && $avg != '') {
					return $avg;
				} else {
					return 0;
				}
			}
		}
		
		function getAttributeTotal($itemType, $itemID, $attribute) {
			global $db;
			$q = "select sum(value) as total from Attributes where itemType = '{$itemType}' and itemID = '{$itemID}' and attribute = '{$attribute}' group by itemID";
			$total = $db->getOne($q);
			if (!PEAR::isError($total)) {
				if ($total != null && $total != '') {
					return $total;
				} else {
					return 0;
				}
			}
		}
	
	}
?>