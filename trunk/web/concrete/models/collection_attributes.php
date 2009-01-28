<?

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Contains the collection attribute key and value objects.
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents metadata added to pages. They key object maps to the "type"
 * of metadata added to pages.
 * @author Andrew Embler <andrew@concrete5.org>
 * @package Pages
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class CollectionAttributeKey extends Object {  
	
	var $akID, $akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues;
	
	function get($akID) {
		if (!is_numeric($akID)) {
			return false;
		}
		
		$db = Loader::db();
		$a = array($akID);
		$q = "select akID, akHandle, akName, akSearchable, akAllowOtherValues, akValues, akType from CollectionAttributeKeys where akID = ?";
		$r = $db->query($q, $a);
	
		if ($r) {
			$cak = new CollectionAttributeKey;
			$row = $r->fetchRow();
			foreach($row as $k => $v) {
				$cak->{$k} = $v;
			}
			return $cak;
		}
	}
	
	public function getByID($akID) {
		return CollectionAttributeKey::get($akID);
	}
	
	function getByHandle($akHandle) {
		$db = Loader::db();
		$a = array($akHandle);
		$q = "select akID, akHandle, akName, akSearchable, akAllowOtherValues, akValues, akType from CollectionAttributeKeys where akHandle = ?";
		$r = $db->query($q, $a);
		
	
		if ($r) {
			$cak = new CollectionAttributeKey;
			$row = $r->fetchRow();
			if (is_array($row)) {
				foreach($row as $k => $v) {
					$cak->{$k} = $v;
				}
			}
			return $cak;
		}
	}
	
	function getCollectionAttributeKeyID() {return $this->akID;}
	function getCollectionAttributeKeyHandle() {return $this->akHandle;}
	function getCollectionAttributeKeyName() {return $this->akName;}
	function isCollectionAttributeKeySearchable() {return $this->akSearchable;}
	function getAllowOtherValues() {return $this->akAllowOtherValues; }
	function getCollectionAttributeKeyValues() {return $this->akValues;}
	function getCollectionAttributeKeyType() {return $this->akType;}
	function isManageableType() {return in_array($this->akType, array("SELECT_ADD"));}
	
	function inUse($akHandle) {
		$db = Loader::db();
		$a = array($akHandle);
		$q = "select akID from CollectionAttributeKeys where akHandle = ?";
		$akID = $db->getOne($q, $a);
		if ($akID > 0) {
			return true;
		}
	}
	
	function removeManageableStoredTerm($term) {
		if ($this->isManageableType()) {
			// this takes a term added by the "select + add one" and clears it out
			// it gets deleted/deselected everywhere
			// TODO
			
		}
	}
	
	/** 
	 * Takes a passed array (or uses $_POST) and retrieves values for the submitted item.
	 * We do this because the array may hold the item differently than just akID_ID (there may be multiple fields, etc...)
	 */
	public function getValueFromPost($arg = false) {
		if (!is_array($arg)) {
			$arg = $_POST;
		}
		switch($this->getCollectionAttributeKeyType()) {
			case "DATE":
				$dt = Loader::helper('form/date_time');
				$val = $dt->translate('akID_' . $this->getCollectionAttributeKeyID());
				break;
			default:
				$val = $arg['akID_' . $this->getCollectionAttributeKeyID()];
				break;
		}
		
		return $val;
	}
	
	function delete() {
		// this removes the record from the CAKeys table, and from the CTypeAttributes tables, but
		// not from the actual CAValues table, nor from the lookup columns
		$db = Loader::db();
		$a = array($this->getCollectionAttributeKeyID());
		$db->query("delete from CollectionAttributeKeys where akID = ?", $a);
		$db->query("delete from PageTypeAttributes where akID = ?", $a);		
	}
	
	function getPreviouslySelectedValues() {
		$db = Loader::db();
		$a = array($this->getCollectionAttributeKeyID());
		$q = "select distinct value from CollectionAttributeValues where akID = ?";
		$r = $db->query($q, $a);
		
		$values = array();
		// [|] = special separate. don't include this in any meta values you have :[
		while ($row = $r->fetchRow()) {
			$v2 = explode('[|]', $row['value']);
			if (is_array($v2)) {
				foreach($v2 as $v2i) {
					if (!in_array($v2i, $values)) {
						$values[] = $v2i;
					}
				}
			} else {
				if (!in_array($row['value'], $values)) {
					$values[] = $row['value'];
				}
			}
		}
		return $values;
	}
	
	function add($akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues=0) {
		$db = Loader::db();
		$a = array($akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues);
		$r = $db->query("insert into CollectionAttributeKeys (akHandle, akName, akSearchable, akValues, akType, akAllowOtherValues) values (?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$akID = $db->Insert_ID();
			
			$ak = CollectionAttributeKey::get($akID);
			if (is_object($ak)) {
				return $ak;
			}
		}
	}
	
	function update($akHandle, $akName, $akSearchable, $akValues, $akType, $akAllowOtherValues=0) {
		Cache::flush();

		$db = Loader::db();
		$a = array($akHandle, $akName, $akSearchable, $akValues, $akType, intval($akAllowOtherValues), $this->akID);
		$db->query("update CollectionAttributeKeys set akHandle = ?, akName = ?, akSearchable = ?, akValues = ?, akType = ?, akAllowOtherValues = ? where akID = ?", $a);
		
		$ak = CollectionAttributeKey::get($this->akID);
		if (is_object($ak)) {
			return $ak;
		}
	}
	
	function updateValues($akValues) {
		Cache::flush();
		$db = Loader::db();
		$a = array($akValues, $this->akID);
		$db->query("update CollectionAttributeKeys set akValues = ? where akID = ?", $a);		
		$ak = CollectionAttributeKey::get($this->akID);
		if (is_object($ak)) {
			return $ak;
		}
	}
	
	//scan all respective collectionAttributeValues rows, and
	function renameValue($oldSpelling,$newSpelling){
		$db = Loader::db();
		$a = array( $this->akID);
		$CAVs=$db->GetArray("Select * FROM CollectionAttributeValues WHERE value LIKE '%".addslashes($oldSpelling)."%' AND akID = ? ", $a);
		foreach($CAVs as $CAV){
			$vals=explode("\n",$CAV['value']);
			$fixedVals=array();
			foreach($vals as $val){
				if($val==$oldSpelling) $fixedVals[]=$newSpelling;
				else $fixedVals[]=$val;
			}
			$a = array(join("\n",$fixedVals), $CAV['akID'], $CAV['cID'], $CAV['cvID'] );
			$db->query("update CollectionAttributeValues set value = ? where akID=? AND cID=? AND cvID=?", $a);	
		}
	}
	
	function getList() {
		$db = Loader::db();
		$q = "select akID from CollectionAttributeKeys order by akID asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = CollectionAttributeKey::get($row['akID']);
		}
		return $la;
	}
	
	static function getNewValueEmptyFieldTxt(){
		return t('Other');
	}
		
}

?>