<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class SelectAttributeTypeController extends AttributeTypeController  {

	private $akSelectAllowMultipleValues;
	private $akSelectAllowOtherValues;
	private $akSelectOptionDisplayOrder;
	
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
			foreach($data['atSelectNewOption'] as $newoption) {
				$optobj = SelectAttributeTypeOption::add($this->attributeKey, $newoption, 1);
				$data['atSelectOptionID'][] = $optobj->getSelectAttributeOptionID();
			}
		}

		if (is_array($data['atSelectOptionID'])) {
			$db = Loader::db();
			$db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
			foreach($data['atSelectOptionID'] as $optID) {
				$db->Execute('insert into atSelectOptionsSelected (avID, atSelectOptionID) values (?, ?)', array($this->getAttributeValueID(), $optID));
				if ($this->akSelectAllowMultipleValues == false) {
					break;
				}
			}
		}
	}
	
	// Sets select options for a particular attribute
	// If the $value == string, then 1 item is selected
	// if array, then multiple, but only if the attribute in question is a select multiple
	// In all cases, if new items that don't appear in the list of available options are
	// specified, if the attribute is setup to accept new input, they will be added, otherwise they will be ignored
	public function setValue($value) {
	
	}
	
	public function getValue() {
		$list = $this->getSelectedOptions();
		return $list;	
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
	
	public function getOptions() {
		if (!isset($this->akSelectOptionDisplayOrder)) {
			$this->load();
		}

		$db = Loader::db();
		switch($this->akSelectOptionDisplayOrder) {
			case 'popularity_desc':
				$r = $db->Execute('select ID, value, displayOrder, count(atSelectOptionsSelected.atSelectOptionID) as total from atSelectOptions left join atSelectOptionsSelected on (atSelectOptions.ID = atSelectOptionsSelected.atSelectOptionID) where akID = ? group by ID order by total desc, value asc', array($this->attributeKey->getAttributeKeyID()));
				break;
			case 'alpha_asc':
				$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by value asc', array($this->attributeKey->getAttributeKeyID()));
				break;
			default:
				$r = $db->Execute('select ID, value, displayOrder from atSelectOptions where akID = ? order by displayOrder asc', array($this->attributeKey->getAttributeKeyID()));
				break;
		}
		$options = new SelectAttributeTypeOptionList();
		while ($row = $r->FetchRow()) {
			$opt = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			$options->add($opt);
		}
		return $options;
	}
		
	public function validateKey() {
		$e = parent::validateKey();
		
		// additional validation for select type
		
		$vals = $this->getSelectValuesFromPost();

		if ($vals->count() < 2 && $this->post('akSelectAllowOtherValues') == 0) {
			$e->add(t('A select attribute type must have at least two values, or must allow users to add to it.'));
		}
		
		return $e;
	}
	
	public function saveKey() {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$initialOptionSet = $this->getOptions();
		$selectedPostValues = $this->getSelectValuesFromPost();
		
		$akSelectAllowMultipleValues = $this->post('akSelectAllowMultipleValues');
		$akSelectAllowOtherValues = $this->post('akSelectAllowOtherValues');
		$akSelectOptionDisplayOrder = $this->post('akSelectOptionDisplayOrder');
		
		if ($this->post('akSelectAllowMultipleValues') != 1) {
			$akSelectAllowMultipleValues = 0;
		}
		if ($this->post('akSelectAllowOtherValues') != 1) {
			$akSelectAllowOtherValues = 0;
		}
		if (!in_array($this->post('akSelectOptionDisplayOrder'), array('display_asc', 'alpha_asc', 'popularity_desc'))) {
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
	
}

class SelectAttributeTypeOption extends Object {

	public function __construct($ID, $value, $displayOrder) {
		$this->ID = $ID;
		$this->value = $value;
		$this->displayOrder = $displayOrder;	
	}
	
	public function getSelectAttributeOptionID() {return $this->ID;}
	public function getSelectAttributeOptionValue() {return $this->value;}
	public function getSelectAttributeOptionDisplayOrder() {return $this->displayOrder;}
	public function getSelectAttributeOptionTemporaryID() {return $this->tempID;}
	
	public function __toString() {return $this->value;}
	
	public static function add($ak, $option, $isEndUserAdded = 0) {
		$db = Loader::db();
		// this works because displayorder starts at zero. So if there are three items, for example, the display order of the NEXT item will be 3.
		$displayOrder = $db->GetOne('select count(ID) from atSelectOptions where akID = ?', array($ak->getAttributeKeyID()));			

		$v = array($ak->getAttributeKeyID(), $displayOrder, $option, $isEndUserAdded);
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
		if (is_array($row)) {
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
		if ($this->tempID != false) {
			return SelectAttributeTypeOption::add($ak, $this->value);
		} else {
			$db = Loader::db();
			$db->Execute('update atSelectOptions set value = ? where ID = ?', array($this->value, $this->ID));
			return SelectAttributeTypeOption::getByID($this->ID);
		}
	}
	
}

class SelectAttributeTypeOptionList extends Object implements Iterator {

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
	
	public function __toString() {
		$str = '';
		foreach($this->options as $opt) {
			$str .= $opt->getSelectAttributeOptionValue() . "\n";
		}
		return $str;
	}


}