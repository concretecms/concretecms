<?php 

/**
 * Contains the collection attribute key and value objects.
 * @package Files
 * @author Tony Trupp <tony@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents metadata of a file.
 * @author Tony Trupp <tony@concrete5.org>
 * @package Files
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
 
Loader::model('attributes');
defined('C5_EXECUTE') or die(_("Access Denied."));

class FileAttributeKey extends AttributeKey {
	
	function get($fakID) {
		if (!is_numeric($fakID)) {
			return false;
		}
		
		$db = Loader::db();
		$a = array($fakID);
		$q = "select fakID, akHandle, akName, akValues, akType, akIsEditable, akIsImporterAttribute from FileAttributeKeys where fakID = ?";
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
	
	public function isAttributeKeyEditable() { return $this->akIsEditable;}
	
	
	public function getByHandle($akHandle) {
		$db = Loader::db();
		$akID = $db->GetOne("select fakID from FileAttributeKeys where akHandle = ?", array($akHandle));
		if ($akID > 0) {
			return FileAttributeKey::get($akID);
		} else {
			 // else we check to see if it's listed in the initial registry
			 $ia = FileTypeList::getImporterAttribute($akHandle);
			 if (is_object($ia)) {
			 	// we create this attribute and return it.
			 	return FileAttributeKey::add($akHandle, $ia->akName, false, $ia->akType, 1, $ia->akIsEditable);
			 } else {
			 	$txt = Loader::helper('text');
			 	return FileAttributeKey::add($akHandle, $txt->unhandle($akHandle), false, 'TEXT', 1, 0);
			 }
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
		$q = "select fakID from FileAttributeKeys where akIsImporterAttribute = 0 order by fakID asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = FileAttributeKey::get($row['fakID']);
		}
		return $la;
	}	
	
	function getImporterList($fv = false) {
		$db = Loader::db();
		if (!is_object($fv)) {
			// then we only return file attributes relevant to this file
			$q = "select fakID from FileAttributeKeys where akIsImporterAttribute = 1 order by fakID asc";
			$r = $db->query($q);
		} else {
			$q = "select FileAttributeKeys.fakID from FileAttributeKeys inner join FileAttributeValues on (FileAttributeKeys.fakID = FileAttributeValues.fakID) where akIsImporterAttribute = 1 and fID = ? and fvID = ? order by fakID asc";
			$r = $db->query($q, array($fv->getFileID(), $fv->getFileVersionID()));
		}
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = FileAttributeKey::get($row['fakID']);
		}
		return $la;
	}	
	

	function add($akHandle, $akName, $akValues, $akType, $akIsImporterAttribute = 0, $akIsEditable = 1) {
		$db = Loader::db();
		$a = array($akHandle, $akName, $akValues, $akType, $akIsImporterAttribute, $akIsEditable);
		$r = $db->query("insert into FileAttributeKeys (akHandle, akName, akValues, akType, akIsImporterAttribute, akIsEditable) values (?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$fakID = $db->Insert_ID();
			
			$ak = FileAttributeKey::get($fakID);
			if (is_object($ak)) {
				return $ak;
			}
		}
	}
	
	function update($akHandle, $akName, $akValues, $akType) {
		Cache::flush();

		$db = Loader::db();
		$a = array($akHandle, $akName, $akValues, $akType, $this->fakID);
		$db->query("update FileAttributeKeys set akHandle = ?, akName = ?, akValues = ?, akType = ? where fakID = ?", $a);
		
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

	function outputSearchHTML() {
		$f = Loader::helper("form");
		switch($this->getAttributeKeyType()) {
			case 'NUMBER':
				$html = $f->text('fakID_' . $this->getAttributeKeyID() . '_from');
				$html .= ' ' . t('to') . ' ';
				$html .= $f->text('fakID_' . $this->getAttributeKeyID() . '_to');
				break;
			case 'DATE':
				$dt = Loader::helper('form/date_time');
				$html = $dt->date('fakID_' . $this->getAttributeKeyID() . '_from', false, false);
				$html .= ' ' . t('to') . ' ';
				$html .= $dt->date('fakID_' . $this->getAttributeKeyID() . '_to', false, false);
				break;
			default:
				return $this->outputHTML();
				break;
		}
		return $html;	
	}
	
	function outputHTML($fv = false) {
		$f = Loader::helper("form");
		$value = '';
		if (is_object($fv)) {
			$value = $fv->getAttribute($this);
		}
		
		switch($this->getAttributeKeyType()) {
			case 'NUMBER':
			case 'TEXT':
				$html = $f->text('fakID_' . $this->getAttributeKeyID(), $value);
				break;
			case 'BOOLEAN':
				$html = $f->checkbox('fakID_' . $this->getAttributeKeyID(), 1, ($value == 1));
				$html .= ' ' . t('Yes');
				break;
			case 'SELECT':
				$optionsTmp = explode("\n", $this->getCollectionAttributeKeyValues());
				$options = array('' => '** ' . t('None'));
				foreach($optionsTmp as $o) {
					$options[$o] = $o;
				}
				unset($optionsTmp);
				$html = $f->select('fakID_' . $this->getAttributeKeyID(), $options, $value);
				break;
			case 'SELECT_MULTIPLE':
				$options = explode("\n", $this->getCollectionAttributeKeyValues());
				$values = explode("\n", $value);
				foreach($options as $o) {
					$html .= '<div>';
					$html .= $f->checkbox('fakID_' . $this->getAttributeKeyID() . '[]', $o, in_array($o, $values));
					$html .= ' ' . $o;
					$html .= '</div>';
				}
				
				break;
			case 'DATE':
				$dt = Loader::helper('form/date_time');
				$html = $dt->datetime('fakID_' . $this->getAttributeKeyID(), $value, true);	
				break;
			case 'RATING':
				$dt = Loader::helper('form/rating');
				$html = $dt->rating('fakID_' . $this->getAttributeKeyID(), $value);	
				break;
		}
		return $html;
	}
}