<?php
namespace Concrete\Attribute\Textarea;
use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Attribute\DefaultController;
class Controller extends DefaultController  {
	
	protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));
	
	public $helpers = array('form');
	
	public function saveKey($data) {
		$akTextareaDisplayMode = $data['akTextareaDisplayMode'];
		if (!$akTextareaDisplayMode) {
			$akTextareaDisplayMode = 'text';
		}
		$options = array();
		if ($akTextareaDisplayMode == 'rich_text_custom') {
			$options = $data['akTextareaDisplayModeCustomOptions'];
		}
		$this->setDisplayMode($akTextareaDisplayMode, $options);
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
        $this->requireAsset('jquery/ui');
        $this->requireAsset('redactor');

		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		// switch display type here
		if ($this->akTextareaDisplayMode == 'text' || $this->akTextareaDisplayMode == '') {
			print Loader::helper('form')->textarea($this->field('value'), $value, array('class' => $additionalClass, 'rows' => 5));
		} else {
			$this->addFooterItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>');
			print '<div class="ccm-attribute-textarea-edit">' . Loader::helper('form')->textarea($this->field('value'), $value, array('class' => $additionalClass . ' ccm-advanced-editor-' . $this->attributeKey->getAttributeKeyID())) . '</div>';
			print '<script type="text/javascript">';
			print 'var CCM_EDITOR_SECURITY_TOKEN = "' . Loader::helper('validation/token')->generate('editor') . '";';
			print '$(function() { $(".ccm-advanced-editor-' . $this->attributeKey->getAttributeKeyID() . '").redactor({';
			if ($this->akTextareaDisplayMode == 'rich_text' || ($this->akTextareaDisplayMode == 'rich_text_custom' && in_array('concrete5menu', $this->akTextareaDisplayModeCustomOptions))) {
				print 'plugins: [\'concrete5\'], ';
			}
			if ($this->akTextareaDisplayMode == 'rich_text_custom') {
				print 'buttons: [';
				$buttonGroups = array();

				if (in_array('html', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'html'";
				}
				if (in_array('paragraph_styles', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'formatting'";
				}
				if (in_array('character_styles', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'bold', 'italic', 'underline', 'deleted'";
				}
				$listButtons = "'orderedlist', 'unorderedlist'";
				$indentButtons = "'indent', 'outdent'";
				$richButtons = array();
				if (in_array('lists', $this->akTextareaDisplayModeCustomOptions) && in_array('indent', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = $listButtons . "," . $indentButtons;
				} else {
					if (in_array('lists', $this->akTextareaDisplayModeCustomOptions)) {
						$buttonGroups[] = $listButtons;
					} else {
						$buttonGroups[] = $indentButtons;
					}
				}
				if (in_array('image', $this->akTextareaDisplayModeCustomOptions)) {
					$richButtons[] = "'image'";
				}
				if (in_array('video', $this->akTextareaDisplayModeCustomOptions)) {
					$richButtons[] = "'video'";
				}
				if (in_array('table', $this->akTextareaDisplayModeCustomOptions)) {
					$richButtons[] = "'table'";
				}
				if (in_array('link', $this->akTextareaDisplayModeCustomOptions)) {
					$richButtons[] = "'link'";
				}
				if (count($richButtons) > 0) {
					$buttonGroups[] = implode(",", $richButtons);
				}	
				if (in_array('color', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'fontcolor','backcolor'";
				}
				if (in_array('alignment', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'alignment'";
				}
				if (in_array('horizontalrule', $this->akTextareaDisplayModeCustomOptions)) {
					$buttonGroups[] = "'horizontalrule'";
				}

				$buttons = implode(",'|',", $buttonGroups);
				print $buttons;
				print ']';
			}
			print '}); });</script>';
		}
	}
	
	public function composer() {
		$this->form();
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
	

	public function setDisplayMode($akTextareaDisplayMode, $akTextareaDisplayModeCustomOptions = array()) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$akTextareaDisplayModeCustomOptionsValue = '';
		if (is_array($akTextareaDisplayModeCustomOptions) && count($akTextareaDisplayModeCustomOptions) > 0) {
			$akTextareaDisplayModeCustomOptionsValue = serialize($akTextareaDisplayModeCustomOptions);
		}
		$db->Replace('atTextareaSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akTextareaDisplayMode' => $akTextareaDisplayMode,
			'akTextareaDisplayModeCustomOptions' => $akTextareaDisplayModeCustomOptionsValue
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
        $this->set('akTextareaDisplayModeCustomOptions', array());
		$this->load();
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akTextareaDisplayMode, akTextareaDisplayModeCustomOptions from atTextareaSettings where akID = ?', array($ak->getAttributeKeyID()));
		$this->akTextareaDisplayMode = $row['akTextareaDisplayMode'];
		$this->akTextareaDisplayModeCustomOptions = array();
		if ($row['akTextareaDisplayMode'] == 'rich_text_custom') {
			$this->akTextareaDisplayModeCustomOptions = unserialize($row['akTextareaDisplayModeCustomOptions']);
		}
		$this->set('akTextareaDisplayMode', $this->akTextareaDisplayMode);
		$this->set('akTextareaDisplayModeCustomOptions', $this->akTextareaDisplayModeCustomOptions);
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
