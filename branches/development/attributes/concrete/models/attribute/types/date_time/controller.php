<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DateTimeAttributeTypeController extends AttributeTypeController  {

	public $helpers = array('form');
	
	protected $searchIndexFieldDefinition = 'T NULL';

	public function saveKey() {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$akDateDisplayMode = $this->post('akDateDisplayMode');
				
		// now we have a collection attribute key object above.
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
		$v2 = date('H:i:s', strtotime($v));
		$r = '';
		if ($v2 != '00:00:00') {
			$r .= date(t('g:i A'), strtotime($v));
			$r .= t(' on ' );
		}
		$r .= date(t('m/d/Y'), strtotime($v));
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
		if ($this->akDateDisplayMode == 'date') {
			print $dt->date($this->field('value'), $caValue);
		} else {
			print $dt->datetime($this->field('value'), $caValue);
		}
	}

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDateTime where avID = ?", array($this->getAttributeValueID()));
		return $value;
	}

	public function search() {
		$dt = Loader::helper('form/date_time');
		$html = $dt->date($this->field('from'), false, false);
		$html .= ' ' . t('to') . ' ';
		$html .= $dt->date($this->field('to'), false, false);
		print $html;
	}
	
	public function saveValue($value) {
		$value = date('Y-m-d H:i:s', strtotime($value));
		
		$db = Loader::db();
		$db->Replace('atDateTime', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function saveForm($data) {
		$this->load();
		$dt = Loader::helper('form/date_time');
		if ($this->akDateDisplayMode == 'date') {
			$this->saveValue($data['value']);
		} else {
			$value = $dt->translate('value', $data);
			$this->saveValue($value);
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