<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class SelectAttributeTypeController extends AttributeTypeController  {

	public function type_form() {
		$this->set('derp', 'lkjfs');
		$path1 = $this->getView()->getAttributeTypeURL('type_form.js');
		$path2 = $this->getView()->getAttributeTypeURL('type_form.css');
		$this->addHeaderItem(Loader::helper('html')->javascript($path1));
		$this->addHeaderItem(Loader::helper('html')->css($path2));
		$this->set('form', Loader::helper('form'));
		
		$akSelectValues = $this->getSelectValuesFromPost();

		$this->set('akSelectValues', $akSelectValues);
	}
	
	private function getSelectValuesFromPost() {
		$akSelectValuesArray=array(); 
		foreach($_POST as $key=>$newVal) { 
			if( !strstr($key,'akSelectValue_') || $newVal=='TEMPLATE' ) continue; 
			$originalVal=$_REQUEST['akSelectValueOriginal_'.str_replace('akSelectValue_','',$key)];		
			$akSelectValuesArray[]=$newVal; 
			//change all previous answers
			if($ak) $ak->renameValue($originalVal,$newVal);
		}
		$akSelectValuesArray=array_unique($akSelectValuesArray);
		return $akSelectValuesArray;
	}
	
	public function form() {
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$row = $db->GetRow('select akSelectAllowMultipleValues, akSelectAllowOtherValues from atSelectSettings where akID = ?', $ak->getAttributeKeyID());
		if (is_array($row)) {
			$this->set('akSelectAllowMultipleValues', $row['akSelectAllowMultipleValues']);
			$this->set('akSelectAllowOtherValues', $row['akSelectAllowOtherValues']);			
		}
		$options = $this->getSelectedOptions();
		$selectedOptions = array();
		foreach($options as $opt) {
			$selectedOptions[] = $opt->getSelectAttributeOptionID();
		}
		$this->set('selectedOptions', $selectedOptions);
	}
	
	public function saveForm($data) {
		if (is_array($data['atSelectOptionID'])) {
			$db = Loader::db();
			$db->Execute('delete from atSelectOptionsSelected where avID = ?', array($this->getAttributeValueID()));
			foreach($data['atSelectOptionID'] as $optID) {
				$db->Execute('insert into atSelectOptionsSelected (avID, atSelectOptionID) values (?, ?)', array($this->getAttributeValueID(), $optID));
			}
		}	
	}
	
	public function getValue() {
		$list = $this->getSelectedOptions();
		return $list;	
	}
	
	public function getSelectedOptions() {
		$db = Loader::db();
		$list = new SelectAttributeTypeOptionList();
		$options = $db->GetAll("select ID, value, displayOrder from atSelectOptionsSelected inner join atSelectOptions on atSelectOptionsSelected.atSelectOptionID = atSelectOptions.ID where avID = ? order by displayOrder asc", array($this->getAttributeValueID()));
		foreach($options as $row) {
			$opt = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			$list->add($opt);
		}
		return $list;
	}
	
	public function getOptions() {
		$db = Loader::db();
		$r = $db->Execute('select ID, value, displayOrder from atSelectOptions order by displayOrder asc');
		
		$options = array();
		while ($row = $r->FetchRow()) {
			$opt = new SelectAttributeTypeOption($row['ID'], $row['value'], $row['displayOrder']);
			$options[] = $opt;
		}
		return $options;
	}
	
	public function addKey() {
		parent::addKey();
		$e = $this->get("attributeError");
		
		// additional validation for select type
		
		$vals = $this->getSelectValuesFromPost();
		if (count($vals) < 2 && $this->post('akSelectAllowOtherValues') == 0) {
			$e->add(t('A select attribute type must have at least two values, or must allow users to add to it.'));
		}
		
		if (!$e->has()) {
			$ak = CollectionAttributeKey::add($this->post('akHandle'), $this->post('akName'), $this->post('akIsSearchable'), $this->post('atID'));
			$db = Loader::db();
			
			$akSelectAllowMultipleValues = $this->post('akSelectAllowMultipleValues');
			$akSelectAllowOtherValues = $this->post('akSelectAllowOtherValues');
			
			if ($this->post('akSelectAllowMultipleValues') != 1) {
				$akSelectAllowMultipleValues = 0;
			}
			if ($this->post('akSelectAllowOtherValues') != 1) {
				$akSelectAllowOtherValues = 0;
			}
					
			// now we have a collection attribute key object above.
			$db->Replace('atSelectSettings', array(
				'akID' => $ak->getAttributeKeyID(), 
				'akSelectAllowMultipleValues' => $akSelectAllowMultipleValues, 
				'akSelectAllowOtherValues' => $akSelectAllowOtherValues
			), array('akID'));
			
			// Now we add the options
			$displayOrder = 0;
			foreach($vals as $option) {
				$v = array($ak->getAttributeKeyID(), $displayOrder, $option);
				$db->Execute('insert into atSelectOptions (akID, displayOrder, value) values (?, ?, ?)', $v);
				$displayOrder++;
			}
		}

		$this->set('error', $e);
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
	
	public function __toString() {
		$str = '';
		foreach($this->options as $opt) {
			$str .= $opt->getSelectAttributeOptionValue() . '\n';
		}
		return $str;
	}


}