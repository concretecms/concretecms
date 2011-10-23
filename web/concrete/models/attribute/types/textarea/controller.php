<? defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('attribute/types/default/controller');
Loader::library('3rdparty/htmLawed');

class TextareaAttributeTypeController extends DefaultAttributeTypeController  {
	
	public $helpers = array('form');
	
	public function saveKey($data) {
		$akTextareaDisplayMode = $data['akTextareaDisplayMode'];
		if (!$akTextareaDisplayMode) {
			$akTextareaDisplayMode = 'text';
		}
		$this->setDisplayMode($akTextareaDisplayMode);
	}

	public function getDisplaySanitizedValue() {
		$this->load();
		if ($this->akTextareaDisplayMode == 'text') {
			return parent::getDisplaySanitizedValue();
		}
		return htmLawed(parent::getValue(), array('safe'=>1, 'deny_attribute'=>'style'));
	}
	
	public function form($additionalClass = false) {
		$this->load();
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		$this->addHeaderItem(Loader::helper('html')->javascript('tiny_mce/tiny_mce.js'));
		// switch display type here
		if ($this->akTextareaDisplayMode == 'text' || $this->akTextareaDisplayMode == '') {
			print Loader::helper('form')->textarea($this->field('value'), $value, array('class' => $additionalClass, 'rows' => 5));
		} else {
			$this->addHeaderItem(Loader::helper('html')->css('ccm.dialog.css'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.forms.css'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.filemanager.css'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.search.css'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.menus.css'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.filemanager.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dialog.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.ui.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('jquery.form.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('jquery.liveupdate.js'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.search.js'));
			$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>'); 
			Loader::element('editor_init');
			$editor_mode = strtoupper(str_replace('rich_text_', '', $this->akTextareaDisplayMode));
			Loader::element('editor_config', array('editor_mode' => $editor_mode, 'editor_selector' => 'ccm-advanced-editor-' . $this->attributeKey->getAttributeKeyID()));
			if (in_array($this->akTextareaDisplayMode, array('rich_text', 'rich_text_advanced', 'rich_text_office', 'rich_text_custom'))) {
				Loader::element('editor_controls', array('mode'=>'full'));
			}
			print Loader::helper('form')->textarea($this->field('value'), $value, array('class' => $additionalClass . ' ccm-advanced-editor-' . $this->attributeKey->getAttributeKeyID()));
		}
	}
	
	public function composer() {
		$this->form('span12');
	}

	public function searchForm($list) {
		$db = Loader::db();
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		return $list;
	}
	
	public function search() {
		$f = Loader::helper('form');
		print $f->text($this->field('value'), $this->request('value'));
	}
	

	public function setDisplayMode($akTextareaDisplayMode) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$db->Replace('atTextareaSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akTextareaDisplayMode' => $akTextareaDisplayMode
		), array('akID'), true);
	}
	
	/* 
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	*/
	
	// should have to delete the at thing
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atDefault where avID = ?', array($id));
		}
		
		$db->Execute('delete from atTextareaSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}
	
	public function type_form() {
		$this->load();
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akTextareaDisplayMode from atTextareaSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akTextareaDisplayMode = $row['akTextareaDisplayMode'];
		$this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
	}
	
	public function exportKey($akey) {
		$this->load();
		$akey->addChild('type')->addAttribute('mode', $this->akTextareaDisplayMode);
		return $akey;
	}

	public function importKey($akey) {
		if (isset($akey->type)) {
			$data['akTextareaDisplayMode'] = $akey->type['mode'];
			$this->saveKey($data);
		}
	}
	
	public function duplicateKey($newAK) {
		$this->load();
		$db = Loader::db();
		$db->Replace('atTextareaSettings', array(
			'akID' => $newAK->getAttributeKeyID(), 
			'akTextareaDisplayMode' => $this->akDateDisplayMode
		), array('akID'), true);
	}
}
