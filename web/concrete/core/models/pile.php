<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* Essentially a user's scrapbook, a pile is an object used for clumping bits of content together around a user account.
* Piles currently only contain blocks but they could also contain collections. Any bit of content inside a user's pile
* can be reordered, etc... although no public interface makes use of much of this functionality.
* @package Utilities
*
*/
class Concrete5_Model_Pile extends Object {

	var $pID, $uID, $isDefault, $name, $state, $timestamp;

	function getUserID() {return $this->uID;}
	function getPileID() {return $this->pID;}
	function isDefault() {return $this->isDefault;}
	function getPileName() {return $this->name;}
	function getPileState() {return $this->state;}

	function get($pID) {
		$db = Loader::db();
		$v = array($pID);
		$q = "select pID, uID, isDefault, name, state from Piles where pID = ?";
		$r = $db->query($q, $v);
		$row = $r->fetchRow();

		$p = new Pile;
		if(is_array($row)) foreach ($row as $k => $v) {
			$p->{$k} = $v;
		}
		return $p;
	}

	function create($name) {
		$db = Loader::db();
		$u = new User();
		$v = array($u->getUserID(), 0, $name, 'READY');
		$q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
		$r = $db->query($q, $v);
		if ($r) {
			$pID = $db->Insert_ID();
			return Pile::get($pID);
		}
	}
	
	function getOrCreate($name) {
		$db = Loader::db();
		$u = new User();
		$v = array($name, $u->getUserID());
		$q = "select pID from Piles where name = ? and uID = ?";
		$pID = $db->getOne($q, $v);
		
		if ($pID > 0) {
			return Pile::get($pID);
		}
		
		$v = array($u->getUserID(), 0, $name, 'READY');
		$q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
		$r = $db->query($q, $v);
		if ($r) {
			$pID = $db->Insert_ID();
			return Pile::get($pID);
		}
	}

	function createDefaultPile() {
		
		$db = Loader::db();
		// for the sake of data integrity, we're going to ensure that a general pile does not exist
		$u = new User();
		if ($u->isRegistered()) {
			$v = array($u->getUserID(), 1);
			$q = "select pID from Piles where uID = ? and isDefault = ?";
		}
		$pID = $db->getOne($q, $v);
		if ($pID > 0) {
			$p = new Pile($pID);
			return $p;
		} else {
			// create a new one
			$v = array($u->getUserID(), 1, null, 'READY');
			$q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
			$r = $db->query($q, $v);
			if ($r) {
				$pID = $db->Insert_ID();
				return Pile::get($pID);
			}
		}
	}
	
	function inPile($obj) {
		$db = Loader::db();
		$v = array();
		$class = strtoupper(get_class($obj));
		switch($class) {
			case "COLLECTION":
				$v = array("COLLECTION", $obj->getCollectionID());
				break;
			case "BLOCK":
				$v = array("BLOCK", $obj->getBlockID());
				break;
		}
		$v[] = $this->getPileID();
		$q = "select pcID from PileContents where itemType = ? and itemID = ? and pID = ?";
		$pcID = $db->getOne($q, $v);
		
		return ($pcID > 0);
	}

	function getDefault() {
		$db = Loader::db();
		// checks to see if we're registered, or if we're a visitor. Either way, we get a pile entry
		$u = new User();
		if ($u->isRegistered()) {
			$v = array($u->getUserID(), 1);
			$q = "select pID from Piles where uID = ? and isDefault = ?";
		}
		$pID = $db->getOne($q, $v);
		if ($pID > 0) {
			$p = Pile::get($pID);
			return $p;
		} else {
			// create a new one
			$p = Pile::createDefaultPile();
			return $p;
		}
	}

	function getMyPiles() {
		$db = Loader::db();
		
		$u = new User();
		if ($u->isRegistered()) {
			$v = array($u->getUserID());
			$q = "select pID from Piles where uID = ? order by name asc";
		}

		$piles = array();
		$r = $db->query($q, $v);
		if ($r) {
			while ($row = $r->fetchRow()) {
				$piles[] = Pile::get($row['pID']);
			}
		}

		return $piles;
	}

	function isMyPile() {
		$u = new User();
		
		if ($u->isRegistered()) {
			return $this->getUserID() == $u->getUserID();
		}
	}

	function delete() {
		$db = Loader::db();
		$v = array($this->pID);
		$q = "delete from Piles where pID = ?";
		$db->query($q, $v);
		$q2 = "delete from PileContents where pID = ?";
		$db->query($q, $v);
	}

	function getPileLength() {
		$db = Loader::db();
		$q = "select count(pcID) from PileContents where pID = ?";
		$v = array($this->pID);
		$r = $db->getOne($q, $v);
		if ($r > 0) {
			return $r;
		} else {
			return 0;
		}
	}

	function getPileContentObjects($display = 'display_order') {
		$pc = array();
		$db = Loader::db();
		switch($display) {
			case 'display_order_date':
				$order = 'displayOrder asc, timestamp desc';
				break;		
			case 'date_desc':
				$order = 'timestamp desc';
				break;
			default:
				$order = 'displayOrder asc';
				break;
		}
		
		$v = array($this->pID);
		$q = "select pcID from PileContents where pID = ? order by {$order}";
		$r = $db->query($q, $v);
		while($row = $r->fetchRow()) {
			$pc[] = PileContent::get($row['pcID']);
		}
		return $pc;
	}
	
	function add(&$obj, $quantity = 1) {
		$db = Loader::db();
		$existingPCID = $this->getPileContentID($obj);
		$v1 = array($this->pID);
		$q1 = "select max(displayOrder) as displayOrder from PileContents where pID = ?";
		$currentDO = $db->getOne($q1, $v1);
		$displayOrder = $currentDO + 1;
		if (!$existingPCID) {
			switch(strtolower(get_class($obj))) {
				case "page":
					$v = array($this->pID, $obj->getCollectionID(), "COLLECTION", $quantity, $displayOrder);
					break;
				case "block":
					$v = array($this->pID, $obj->getBlockID(), "BLOCK", $quantity, $displayOrder);
					break;
				case "pilecontent":
					$v = array($this->pID, $obj->getItemID(), $obj->getItemType(), $obj->getQuantity(), $displayOrder);
					break;
			}
			$q = "insert into PileContents (pID, itemID, itemType, quantity, displayOrder) values (?, ?, ?, ?, ?)";
			$r = $db->query($q, $v);
			if ($r) {
				$pcID = $db->Insert_ID();
				return $pcID;
			}
		} else {
			return $existingPCID;
		}
	}
	
	function remove(&$obj, $quantity = 1) {
		$db = Loader::db();
		switch(strtolower(get_class($obj))) {
			case "page":
				$v = array($this->pID, $obj->getCollectionID(), "COLLECTION");
				break;
			case "block":
				$v = array($this->pID, $obj->getBlockID(), "BLOCK");
				break;
			case "pilecontent":
				$v = array($this->pID, $obj->getItemID(), $obj->getItemType());
				break;
		}
		
		$q = "select quantity from PileContents where pID = ? and itemID = ? and itemType = ?";
		$exQuantity = $db->getOne($q, $v);
		if ($exQuantity > $quantity) {
			$db->query("update PileContent set quantity = quantity - {$quantity} where pID = ? and itemID = ? and itemType = ?", $v);
		} else {
			$db->query("delete from PileContents where pID = ? and itemID = ? and itemType = ?", $v);
		}
	}

	function getPileContentID(&$obj) {
		$db = Loader::db();
		switch(strtolower(get_class($obj))) {
			case "page":
				$v = array($this->pID, $obj->getCollectionID(), "COLLECTION");
				$q = "select pcID from PileContents where pID = ? and itemID = ? and itemType = ?";
				$pcID = $db->getOne($q, $v);
				if ($pcID > 0) {
					return $pcID;
				}
				break;
		}
	}

	function rescanDisplayOrder() {
		$db = Loader::db();
		$v = array($this->pID);
		$q = "select pcID from PileContents where pID = ? order by displayOrder asc";
		$r = $db->query($q, $v);
		$currentDisplayOrder = 0;
		while($row = $r->fetchRow()) {
			$v1 = array($currentDisplayOrder, $row['pcID']);
			$q1 = "update PileContents set displayOrder = ? where pcID = ?";
			$db->query($q1, $v1);
			$currentDisplayOrder++;
		}
	}
}

