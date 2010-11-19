<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class DateTimeAttributeTypeController extends AttributeTypeController  {

	public $helpers = array('form');
	
	protected $searchIndexFieldDefinition = 'T NULL';

	public function saveKey($data) {
		$akDateDisplayMode = $data['akDateDisplayMode'];
		if (!$akDateDisplayMode) {
			$akDateDisplayMode = 'date_time';
		}
		$this->setDisplayMode($akDateDisplayMode);
	}
	
	public function setDisplayMode($akDateDisplayMode) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$db->Replace('atDateTimeSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akDateDisplayMode' => $akDateDisplayMode
		), array('akID'), true);
	}
	
	public function type_form() {
		$this->load();
	}

	public function getDisplayValue() {
		$v = $this->getValue();
		if ($v == '' || $v == false) {
			return '';
		}
		$v2 = date('H:i:s', strtotime($v));
		$r = '';
		if ($v2 != '00:00:00') {
			$r .= date(DATE_APP_DATE_ATTRIBUTE_TYPE_T, strtotime($v));
			$r .= t(' on ' );
		}
		$r .= date(DATE_APP_DATE_ATTRIBUTE_TYPE_MDY, strtotime($v));
		return $r;
	}
	
	public function searchForm($list) {
		$dateFrom = $this->request('from');
		$dateTo = $this->request('to');
		if ($dateFrom) {
			$dateFrom = date('Y-m-d', strtotime($dateFrom));
			$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $dateFrom, '>=');
		}
		if ($dateTo) {
			$dateTo = date('Y-m-d', strtotime($dateTo));
			$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $dateTo, '<=');
		}
		return $list;
	}
	
	public function form() {
		$this->load();
		$dt = Loader::helper('form/date_time');
		$caValue = $this->getValue();
		switch($this->akDateDisplayMode) {
			case 'text':
				$form = Loader::helper('form');
				print $form->text($this->field('value'), $this->getDisplayValue());
				break;
			case 'date':
				print $dt->date($this->field('value'), $caValue);
				break;
			default:
				print $dt->datetime($this->field('value'), $caValue);
				break;
		}
	}

	public function validateForm($data) {
		return $data['value'] != '';
	}

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDateTime where avID = ?", array($this->getAttributeValueID()));
		return $value;
	}

	public function search() {
		$dt = Loader::helper('form/date_time');
		$html = $dt->date($this->field('from'), $this->request('from'), false);
		$html .= ' ' . t('to') . ' ';
		$html .= $dt->date($this->field('to'), $this->request('to'), false);
		print $html;
	}
	
	public function saveValue($value) {
		if ($value != '') {
			$value = date('Y-m-d H:i:s', strtotime($value));
		} else {
			$value = null;
		}
		
		$db = Loader::db();
		$db->Replace('atDateTime', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}

	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Execute('insert into atDateTimeSettings (akID, akDateDisplayMode) values (?, ?)', array($newAK->getAttributeKeyID(), $this->akDateDisplayMode));	
	}
	
	public function saveForm($data) {
		$this->load();
		$dt = Loader::helper('form/date_time');
		switch($this->akDateDisplayMode) {
			case 'text':
				$this->saveValue($data['value']);
				break;
			case 'date':
			case 'date_time':
				$value = $dt->translate('value', $data);
				$this->saveValue($value);
				break;
		}
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akDateDisplayMode from atDateTimeSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akDateDisplayMode = $row['akDateDisplayMode'];

		$this->set('akDateDisplayMode', $this->akDateDisplayMode);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atDateTime where avID = ?', array($id));
		}
	}
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atDateTime where avID = ?', array($this->getAttributeValueID()));
	}

}