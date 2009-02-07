<?

/**
 * Contains the collection attribute key and value objects.
 * @package models
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents metadata of a file.
 * @author Tony Trupp <tony@concrete5.org>
 * @package models
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
 
Loader::model('attributes');

class FileAttributeKey extends AttributeKey {

	// reserved keys
	const K_WIDTH = 'width';
	const K_HEIGHT = 'height';
	
	function get($fakID) {
		if (!is_numeric($fakID)) {
			return false;
		}
		
		$db = Loader::db();
		$a = array($fakID);
		$q = "select fakID, akHandle, akName, akAllowOtherValues, akValues, akType from FileAttributeKeys where fakID = ?";
		$r = $db->query($q, $a);
	
		if ($r) {
			$cak = new FileAttributeKey;
			$row = $r->fetchRow();
			if(!$row) return false;
			foreach($row as $k => $v) {
				$cak->{$k} = $v;
			}
			return $cak;
		}
	}	
	
	function getAttributeKeyID() { return $this->fakID; }
	
	function getList() {
		$db = Loader::db();
		$q = "select fakID from FileAttributeKeys order by fakID asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = FileAttributeKey::get($row['fakID']);
		}
		return $la;
	}	
	
	function getUserAddedList() {
		$db = Loader::db();
		$q = "select fakID from FileAttributeKeys where akIsUserAdded = 1 order by fakID asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = FileAttributeKey::get($row['fakID']);
		}
		return $la;
	}	
	

	function add($akHandle, $akName, $akValues, $akType, $akAllowOtherValues=0, $akIsUserAdded = 0) {
		$db = Loader::db();
		$a = array($akHandle, $akName, $akValues, $akType, $akAllowOtherValues, $akIsUserAdded);
		$r = $db->query("insert into FileAttributeKeys (akHandle, akName, akValues, akType, akAllowOtherValues, akIsUserAdded) values (?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$fakID = $db->Insert_ID();
			
			$ak = FileAttributeKey::get($fakID);
			if (is_object($ak)) {
				return $ak;
			}
		}
	}
	
	function update($akHandle, $akName, $akValues, $akType, $akAllowOtherValues=0) {
		Cache::flush();

		$db = Loader::db();
		$a = array($akHandle, $akName, $akValues, $akType, intval($akAllowOtherValues), $this->fakID);
		$db->query("update FileAttributeKeys set akHandle = ?, akName = ?, akValues = ?, akType = ?, akAllowOtherValues = ? where fakID = ?", $a);
		
		$ak = FileAttributeKey::get($this->fakID);
		if (is_object($ak)) {
			return $ak;
		}
	}	
	
	function delete(){ 
		$db = Loader::db();
		$a = array($this->getAttributeKeyID());
		$db->query("delete from FileAttributeKeys where fakID = ?", $a);
		$db->query("delete from FileAttributeValues where fakID = ?", $a);		
	}
	
	//scan all respective collectionAttributeValues rows, and
	function renameValue($oldSpelling,$newSpelling){
		$db = Loader::db();
		$a = array( $this->fakID);
		$CAVs=$db->GetArray("Select * FROM FileAttributeValues WHERE value LIKE '%".addslashes($oldSpelling)."%' AND fakID = ? ", $a);
		foreach($CAVs as $CAV){
			$vals=explode("\n",$CAV['value']);
			$fixedVals=array();
			foreach($vals as $val){
				if($val==$oldSpelling) $fixedVals[]=$newSpelling;
				else $fixedVals[]=$val;
			}
			$a = array(join("\n",$fixedVals), $CAV['fakID'], $CAV['fID'], $CAV['fvID'] );
			$db->query("update FileAttributeValues set value = ? where fakID=? AND fID=? AND fvID=?", $a);	
		}
	}	
	
	function inUse($akHandle) {
		$db = Loader::db();
		$a = array($akHandle);
		$q = "select fakID from FileAttributeKeys where akHandle = ?";
		$fakID = $db->getOne($q, $a);
		if ($fakID > 0) {
			return true;
		}
	}	
}