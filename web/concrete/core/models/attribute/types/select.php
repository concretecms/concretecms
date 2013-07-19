<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AttributeType_Select extends AttributeTypeController  {

	private $akSelectAllowMultipleValues;
	private $akSelectAllowOtherValues;
	private $akSelectOptionDisplayOrder;

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function type_form() {
		$path1 = $this->getView()->getAttributeTypeURL('type_form.js');
		$path2 = $this->getView()->getAttributeTypeURL('type_form.css');
		$this->addHeaderItem(Loader::helper('html')->javascript($path1));
		$this->addHeaderItem(Loader::helper('html')->css($path2));
		$this->set('form', Loader::helper('form'));		
		$this->load();
		//$akSelectValues = $this->getSelectValuesFromPost();
		//$this->set('akSelectValues', $akSelectValues);
		
		if ($this->isPost()) {
			$akSelectValues = $this->getSelectValuesFromPost();
			$this->set('akSelectValues', $akSelectValues);
		} else if (isset($this->attributeKey)) {
			$options = $this->getOptions();
			$this->set('akSelectValues', $options);
		} else {
			$this->set('akSelectValues', array());
		}
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akSelectAllowMultipleValues, akSelectOptionDisplayOrder, akSelectAllowOtherValues from atSelectSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akSelectAllowMultipleValues = $row['akSelectAllowMultipleValues'];
		$this->akSelectAllowOtherValues = $row['akSelectAllowOtherValues'];
		$this->akSelectOptionDisplayOrder = $row['akSelectOptionDisplayOrder'];

		$this->set('akSelectAllowMultipleValues', $this->akSelectAllowMultipleValues);
		$this->set('akSelectAllowOtherValues', $this->akSelectAllowOtherValues);			
		$this->set('akSelectOptionDisplayOrder', $this->akSelectOptionDisplayOrder);			
	}

	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atSelectSettings (akID, akSelectAllowMultipleValues, akSelectOptionDisplayOrder, akSelectAllowOtherValues) values (?, ?, ?, ?)', array($newAK->getAttributeKeyID(), $this->akSelectAllowMultipleValues, $this->akSelectOptionDisplayOrder, $this->akSelectAllowOtherValues));	
		$r = $db->Execute('select value, displayOrder, isEndUserAdded from atSelectOptions where akID = ?', $this->getAttributeKey()->getAttributeKeyID());
		while ($row = $r->FetchRow()) {
			$db->Execute('insert into atSelectOptions (akID, value, displayOrder, isEndUserAdded) values (?, ?, ?, ?)', array(
				$newAK->getAttributeKeyID(),
				$row['value'],
				$row['displayOrder'],
				$row['isEndUserAdded']
			));
		}
	}
	
	public function exportKey($akey) {
		$this->load();
		$db = Loader::db();
		$type = $akey->addChild('type');
		$type->addAttribute('allow-multiple-values', $this->akSelectAllowMultipleValues);
		$type->addAttribute('display-order', $this->akSelectOptionDisplayOrder);
		$type->addAttribute('allow-other-values', $this->akSelectAllowOtherValues);
		$r = $db->Execute('select value, displayOrder, isEndUserAdded from atSelectOptions where akID = ? order by displayOrder asc', $this->getAttributeKey()->getAttributeKeyID());
		$options = $type->addChild('options');
		while ($row = $r->FetchRow()) {
			$opt = $options->addChild('option');
			$opt->addAttribute('value', $row['value']);
			$opt->addAttribute('is-end-user-added', $row['isEndUserAdded']);
		}
		return $akey;
	}
	
	public function exportValue($akn) {
		$list = $this->getSelectedOptions();
		if ($list->count() > 0) {
			$av = $akn->addChild('value');
			foreach($list as $l) {
				$av->addChild('option', (string) $l);
			}
		}
	}
	
	public function importValue(SimpleXMLElement $akv) {
		if (isset($akv->value)) {
			$vals = array();
			foreach($akv->value->children() as $ch) {
				$vals[] = (string) $ch;
			}
			return $vals;
		}
	}
	
	public function importKey($akey) {
		if (isset($akey->type)) {
			$akSelectAllowMultipleValues = $akey->type['allow-multiple-values'];
			$akSelectOptionDisplayOrder = $akey->type['display-order'];
			$akSelectAllowOtherValues = $akey->type['allow-other-values'];
			$db = Loader::db();
			$db->Replace('atSelectSettings', array(
				'akID' => $this->attributeKey->getAttributeKeyID(), 
				'akSelectAllowMultipleValues' => $akSelectAllowMultipleValues, 
				'akSelectAllowOtherValues' => $akSelectAllowOtherValues,
				'akSelectOptionDisplayOrder' => $akSelectOptionDisplayOrder
			), array('akID'), true);

			if (isset($akey->type->options)) {
				foreach($akey->type->options->children() as $option) {
					SelectAttributeTypeOption::add($this->attributeKey, $option['value'], $option['is-end-user-added']);
				}
			}
		}
	}
	
	private function getSelectValuesFromPost() {
		$options = new SelectAttributeTypeOptionList();
		$displayOrder = 0;		
		foreach($_POST as $key => $value) {
			if( !strstr($key,'akSelectValue_') || $value=='TEMPLATE' ) continue; 
			$opt = false;
			// strip off the prefix to get the ID
			$id = substr($key, 14);
			// now we determine from the post whether this is a new option
			// or an existing. New ones have this value from in the akSelectValueNewOption_ post field
			if ($_POST['akSelectValueNewOption_' . $id] == $id) {
				$opt = new SelectAttributeTypeOption(0, $value, $displayOrder);
				$opt->tempID = $id;
			} else if ($_POST['akSelectValueExistingOption_' . $id] == $id) {
				$opt = new SelectAttributeTypeOption($id, $value, $displayOrder);
			}
			
			if (is_object($opt)) {
				$options->add($opt);
				$displayOrder++;
			}
		}
		
		return $options;
	}
	
	public function form() {
		$this->load();
		$options = $this->getSelectedOptions();
		$selectedOptions = array();
		foreach($options as $opt) {
			$selectedOptions[] = $opt->getSelectAttributeOptionID();
			$selectedOptionValues[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionValue();
		}
		$this->set('selectedOptionValues',$selectedOptionValues);
		$this->set('selectedOptions', $selectedOptions);
		$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
	}
	
	public function search() {
		$this->load();	
		$selectedOptions = $this->request('atSelectOptionID');
		if (!is_array($selectedOptions)) {
			$selectedOptions = array();
		}
		$this->set('selectedOptions', $selectedOptions);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
	}

	public function deleteKey() {
		$db = Loader::db();
		$db->Execute('delete from atSelectSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
		$r = $db->Execute('select ID from atSelectOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from atSelectOptionsSelected where atSelectOptionID = ?', array($row['ID']));
		}
		$db->Execute('delete from atSelectOptions where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}

	public function saveForm($data) {
		$this->load();
		
		if ($this->akSelectAllowOtherValues && is_array($data['atSelectNewOption'])) {
			$options = $this->getOptions();
						
			foreach($data['atSelectNewOption'] as $newoption) {
				// check for duplicates
				$existing = false;
				foreach($options as $opt) {
					if(strtolower(trim($newoption)) == strtolower(trim($opt->getSelectAttributeOptionValue(false)))) {
						$existing = $opt;
						break;
					}
				}
				if($existing instanceof SelectAttributeTypeOption) {
					$data['atSelectOptionID'][] = $existing->getSelectAttributeOptionID();
				} else {
					$optobj = SelectAttributeTypeOption::add($this->attributeKey, $newoption, 1);
					$data['atSelectOptionID'][] = $optobj->getSelectAttributeOptionID();
				}
			}
		}

		if(is_array($data['atSelectOptionID'])) {
			$data['atSelectOptionID'] = array_unique($data['atSelectOptionID']);
		}		
		$db = Loader::db();
		$db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
		if (is_array($data['atSelectOptionID'])) {
			foreach($data['atSelectOptionID'] as $optID) {
				if ($optID > 0) {
					$db->Execute('insert into atSelectOptionsSelected (avID, atSelectOptionID) values (?, ?)', array($this->getAttributeValueID(), $optID));
					if ($this->akSelectAllowMultipleValues == false) {
						break;
					}
				}
			}
		}
	}
	
	// Sets select options for a particular attribute
	// If the $value == string, then 1 item is selected
	// if array, then multiple, but only if the attribute in question is a select multiple
	// Note, items CANNOT be added to the pool (even if the attribute allows it) through this process.
	public function saveValue($value) {
		$db = Loader::db();
		$this->load();
		$options = array();		
		
		if (is_array($value) && $this->akSelectAllowMultipleValues) {
			foreach($value as $v) {
				$opt = SelectAttributeTypeOption::getByValue($v, $this->attributeKey);
				if (is_object($opt)) {
					$options[] = $opt;	
				}
			}
		} else {
			if (is_array($value)) {
				$value = $value[0];
			}
			
			$opt = SelectAttributeTypeOption::getByValue($value, $this->attributeKey);
			if (is_object($opt)) {
				$options[] = $opt;	
			}
		}
		
		$db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
		if (count($options) > 0) {
			foreach($options as $opt) {
				$db->Execute('insert into atSelectOptionsSelected (avID, atSelectOptionID) values (?, ?)', array($this->getAttributeValueID(), $opt->getSelectAttributeOptionID()));
				if ($this->akSelectAllowMultipleValues == false) {
					break;
				}
			}
		}
	}

	
	public function getDisplayValue() {
		$list = $this->getSelectedOptions();
		$html = '';
		foreach($list as $l) {
			$html .= $l . '<br/>';
		}
		return $html;
	}

	public function getDisplaySanitizedValue() {
		$list = $this->getSelectedOptions();
		$html = '';
		foreach($list as $l) {
			$html .= $l->getSelectAttributeOptionValue() . '<br/>';
		}
		return $html;
	}
	
	public function validateForm($p) {
		$this->load();
		$options = $this->request('atSelectOptionID');
		if ($this->akSelectAllowOtherValues) {
			$options = array_filter((Array) $this->request('atSelectNewOption'));
			if (is_array($options) && count($options) > 0) {
				return true;
			} else if (array_shift($this->request('atSelectOptionID')) != null) {
				return true;
			}
		}
		if ($this->akSelectAllowMultipleValues) {
			return count($options) > 0;
		} else {
			if ($options[0] != false) {
				return $options[0] > 0;
			}
		}
		return false;
	}
	
	public function searchForm($list) {
		$options = $this->request('atSelectOptionID');
		$optionText = array();
		$db = Loader::db();
		$tbl = $this->attributeKey->getIndexedSearchTable();
		if (!is_array($options)) {
			return $list;
		}
		foreach($options as $id) {
			if ($id > 0) {
				$opt = SelectAttributeTypeOption::getByID($id);
				if (is_object($opt)) {
					$optionText[] = $opt->getSelectAttributeOptionValue(true);
					$optionQuery[] = $opt->getSelectAttributeOptionValue(false);
				}
			}
		}
		if (count($optionText) == 0) {
			return false;
		}
		
		$i = 0;
		foreach($optionQuery as $val) {
			$val = $db->quote('%||' . $val . '||%');
			$multiString .= 'REPLACE(' . $tbl . '.ak_' . $this->attributeKey->getAttributeKeyHandle() . ', "\n", "||") like ' . $val . ' ';
			if (($i + 1) < count($optionQuery)) {
				$multiString .= 'OR ';
			}
			$i++;
		}
		$list->filter(false, '(' . $multiString . ')');
		return $list;
	}
	
	public function getValue() {
		$list = $this->getSelectedOptions();
		return $list;	
	}
	
    public function getSearchIndexValue() {
        $str = "\n";
        $list = $this->getSelectedOptions();
        foreach($list as $l) {
            $l = (is_object($l) && method_exists($l,'__toString')) ? $l->__toString() : $l;
            $str .= $l . "\n";
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }
        return $str;
    }
	
	public function getSelectedOptions() {
		if (!isset($this->akSelectOptionDisplayOrder)) {
			$this->load();
		}
		$db = Loader::db();
		switch($this->akSelectOptionDisplayOrder) {
			case 'popularity_desc':
				$options = $db->GetAll("select ID, value, displayOrder, (select count(s2.atSelectOptionID) from atSelectOptionsSelected s2 where s2.atSelectOptionID = ID) as total from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by total desc, value asc", array($this->getAttributeValueID()));
				break;
			case 'alpha_asc':
				$options = $db->GetAll("select ID, value, displayOrder from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by value asc", array($this->getAttributeValueID()));
				break;
			default:
				$options = $db->GetAll("select ID, value, displayOrder from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by displayOrder asc", array($this->getAttributeValueID()));
				break;
		}
		$db = Loader::db();
		$list = new SelectAttributeTypeOptionList();
		foreach($options as $row) {
			$opt = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			$list->add($opt);
		}
		return $list;
	}
	
	public function action_load_autocomplete_values() {
		$this->load();
		$values = array();
			// now, if the current instance of the attribute key allows us to do autocomplete, we return all the values
		if ($this->akSelectAllowMultipleValues && $this->akSelectAllowOtherValues) {
			$options = $this->getOptions($_GET['term'] . '%');
			foreach($options as $opt) {
				$values[] = $opt->getSelectAttributeOptionValue(false);
			}
		}
		print Loader::helper('json')->encode($values);
	}
	
	public function getOptionUsageArray($parentPage = false, $limit = 9999) {
		$db = Loader::db();
		$q = "select atSelectOptions.value, atSelectOptionID, count(atSelectOptionID) as total from Pages inner join CollectionVersions on (Pages.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) inner join CollectionAttributeValues on (CollectionVersions.cID = CollectionAttributeValues.cID and CollectionVersions.cvID = CollectionAttributeValues.cvID) inner join atSelectOptionsSelected on (atSelectOptionsSelected.avID = CollectionAttributeValues.avID) inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where Pages.cIsActive = 1 and CollectionAttributeValues.akID = ? ";
		$v = array($this->attributeKey->getAttributeKeyID());
		if (is_object($parentPage)) {
			$v[] = $parentPage->getCollectionID();
			$q .= "and cParentID = ?";
		}
		$q .= " group by atSelectOptionID order by total desc limit " . $limit;
		$r = $db->Execute($q, $v);
		$list = new SelectAttributeTypeOptionList();
		$i = 0;
		while ($row = $r->FetchRow()) {
			$opt = new SelectAttributeTypeOption($row['atSelectOptionID'], $row['value'], $i, $row['total']);
			$list->add($opt);
			$i++;
		}		
		return $list;
	}
	
	/**
	 * returns a list of available options optionally filtered by an sql $like statement ex: startswith%
	 * @param string $like
	 * @return SelectAttributeTypeOptionList
	 */
	public function getOptions($like = NULL) {
		if (!isset($this->akSelectOptionDisplayOrder)) {
			$this->load();
		}
		$db = Loader::db();
		switch($this->akSelectOptionDisplayOrder) {
			case 'popularity_desc':
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, value, displayOrder, count(atSelectOptionsSelected.atSelectOptionID) as total 
						from atSelectOptions left join atSelectOptionsSelected on (atSelectOptions.ID = atSelectOptionsSelected.atSelectOptionID) 
						where akID = ? AND atSelectOptions.value LIKE ? group by ID order by total desc, value asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, value, displayOrder, count(atSelectOptionsSelected.atSelectOptionID) as total 
						from atSelectOptions left join atSelectOptionsSelected on (atSelectOptions.ID = atSelectOptionsSelected.atSelectOptionID) 
						where akID = ? group by ID order by total desc, value asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
			case 'alpha_asc':
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? AND atSelectOptions.value LIKE ? order by value asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by value asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
			default:
				if(isset($like) && strlen($like)) {
					$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? AND atSelectOptions.value LIKE ? order by displayOrder asc', array($this->attributeKey->getAttributeKeyID(),$like));
				} else {
					$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by displayOrder asc', array($this->attributeKey->getAttributeKeyID()));
				}
				break;
		}
		$options = new SelectAttributeTypeOptionList();
		while ($row = $r->FetchRow()) {
			$opt = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			$options->add($opt);
		}
		return $options;
	}
		
	public function saveKey($data) {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$initialOptionSet = $this->getOptions();
		$selectedPostValues = $this->getSelectValuesFromPost();
		
		$akSelectAllowMultipleValues = $data['akSelectAllowMultipleValues'];
		$akSelectAllowOtherValues = $data['akSelectAllowOtherValues'];
		$akSelectOptionDisplayOrder = $data['akSelectOptionDisplayOrder'];
		
		if ($data['akSelectAllowMultipleValues'] != 1) {
			$akSelectAllowMultipleValues = 0;
		}
		if ($data['akSelectAllowOtherValues'] != 1) {
			$akSelectAllowOtherValues = 0;
		}
		if (!in_array($data['akSelectOptionDisplayOrder'], array('display_asc', 'alpha_asc', 'popularity_desc'))) {
			$akSelectOptionDisplayOrder = 'display_asc';
		}
				
		// now we have a collection attribute key object above.
		$db->Replace('atSelectSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akSelectAllowMultipleValues' => $akSelectAllowMultipleValues, 
			'akSelectAllowOtherValues' => $akSelectAllowOtherValues,
			'akSelectOptionDisplayOrder' => $akSelectOptionDisplayOrder
		), array('akID'), true);
		
		// Now we add the options
		$newOptionSet = new SelectAttributeTypeOptionList();
		$displayOrder = 0;
		foreach($selectedPostValues as $option) {
			$opt = $option->saveOrCreate($ak);
			if ($akSelectOptionDisplayOrder == 'display_asc') {
				$opt->setDisplayOrder($displayOrder);
			}
			$newOptionSet->add($opt);
			$displayOrder++;
		}
		
		// Now we remove all options that appear in the 
		// old values list but not in the new
		foreach($initialOptionSet as $iopt) {
			if (!$newOptionSet->contains($iopt)) {
				$iopt->delete();
			}
		}
	}

	/**
	 * Convenience methods to retrieve a select attribute key's settings
	 */
	public function getAllowMultipleValues() {
		if (is_null($this->akSelectAllowMultipleValues)) {
			$this->load();
		}
		return $this->akSelectAllowMultipleValues;
	}
	
	public function getAllowOtherValues() {
		if (is_null($this->akSelectAllowOtherValues)) {
			$this->load();
		}
		return $this->akSelectAllowOtherValues;
	}
	
	public function getOptionDisplayOrder() {
		if (is_null($this->akSelectOptionDisplayOrder)) {
			$this->load();
		}
		return $this->akSelectOptionDisplayOrder;
	}
	
}

class Concrete5_Model_SelectAttributeTypeOption extends Object {

	public function __construct($ID, $value, $displayOrder, $usageCount = false) {
		$this->ID = $ID;
		$this->value = $value;
		$this->th = Loader::helper('text');
		$this->displayOrder = $displayOrder;	
		$this->usageCount = $usageCount;	
	}
	
	public function getSelectAttributeOptionID() {return $this->ID;}
	public function getSelectAttributeOptionUsageCount() {return $this->usageCount;}
	public function getSelectAttributeOptionValue($sanitize = true) {
		if (!$sanitize) {
			return $this->value;
		} else {
			return $this->th->specialchars($this->value);
		}
	}
	public function getSelectAttributeOptionDisplayOrder() {return $this->displayOrder;}
	public function getSelectAttributeOptionTemporaryID() {return $this->tempID;}
	
	public function __toString() {return $this->value;}
	
	public static function add($ak, $option, $isEndUserAdded = 0) {
		$db = Loader::db();
		$th = Loader::helper('text');
		// this works because displayorder starts at zero. So if there are three items, for example, the display order of the NEXT item will be 3.
		$displayOrder = $db->GetOne('select count(ID) from atSelectOptions where akID = ?', array($ak->getAttributeKeyID()));			

		$v = array($ak->getAttributeKeyID(), $displayOrder, $th->sanitize($option), $isEndUserAdded);
		$db->Execute('insert into atSelectOptions (akID, displayOrder, value, isEndUserAdded) values (?, ?, ?, ?)', $v);
		
		return SelectAttributeTypeOption::getByID($db->Insert_ID());
	}
	
	public function setDisplayOrder($num) {
		$db = Loader::db();
		$db->Execute('update atSelectOptions set displayOrder = ? where ID = ?', array($num, $this->ID));
	}
	
	public static function getByID($id) {
		$db = Loader::db();
		$row = $db->GetRow("select ID, displayOrder, value from atSelectOptions where ID = ?", array($id));
		if (isset($row['ID'])) {
			$obj = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			return $obj;
		}
	}
	
	public static function getByValue($value, $ak = false) {
		$db = Loader::db();
		if (is_object($ak)) {
			$row = $db->GetRow("select ID, displayOrder, value from atSelectOptions where value = ? and akID = ?", array($value, $ak->getAttributeKeyID()));
		} else {
			$row = $db->GetRow("select ID, displayOrder, value from atSelectOptions where value = ?", array($value));
		}
		if (isset($row['ID'])) {
			$obj = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			return $obj;
		}
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from atSelectOptions where ID = ?', array($this->ID));
		$db->Execute('delete from atSelectOptionsSelected where atSelectOptionID = ?', array($this->ID));
	}
	
	public function saveOrCreate($ak) {
		if ($this->tempID != false || $this->ID==0) {
			return SelectAttributeTypeOption::add($ak, $this->value);
		} else {
			$db = Loader::db();
			$th = Loader::helper('text');
			$db->Execute('update atSelectOptions set value = ? where ID = ?', array($th->sanitize($this->value), $this->ID));
			return SelectAttributeTypeOption::getByID($this->ID);
		}
	}
	
}

class Concrete5_Model_SelectAttributeTypeOptionList extends Object implements Iterator {

	private $options = array();
	
	public function add(SelectAttributeTypeOption $opt) {
		$this->options[] = $opt;
	}
	
	public function rewind() {
		reset($this->options);
	}
	
	public function current() {
		return current($this->options);
	}
	
	public function key() {
		return key($this->options);
	}
	
	public function next() {
		next($this->options);
	}
	
	public function valid() {
		return $this->current() !== false;
	}
	
	public function count() {return count($this->options);}
	
	public function contains(SelectAttributeTypeOption $opt) {
		foreach($this->options as $o) {
			if ($o->getSelectAttributeOptionID() == $opt->getSelectAttributeOptionID()) {
				return true;
			}
		}
		
		return false;
	}
	
	public function get($index) {
		return $this->options[$index];
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function __toString() {
		$str = '';
		$i = 0;
		foreach($this->options as $opt) {
			$str .= $opt->getSelectAttributeOptionValue();
			$i++;
			if ($i < count($this->options)) {
				$str .= "\n";
			}
		}
		return $str;
	}


}
