<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockComposerControl extends ComposerControl {

	protected $btID;
	protected $cmpControlTypeHandle = 'block';
	
	public function setBlockTypeID($btID) {
		$this->btID = $btID;
		$this->setComposerControlIdentifier($btID);
	}

	public function getBlockTypeID() {
		return $this->btID;
	}

	public function getComposerControlCustomTemplates() {
		$bt = BlockType::getByID($this->btID);
		$txt = Loader::helper('text');
		$templates = array();
		if (is_object($bt)) {
			$blocktemplates = $bt->getBlockTypeComposerTemplates();
			if (is_array($blocktemplates)) {
				foreach($blocktemplates as $tpl) {
	                if (strpos($tpl, '.') !== false) {
	                    $name = substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
	                } else {
	                	$name = $txt->unhandle($tpl);
	                }
					$templates[] = new ComposerControlCustomTemplate($tpl, $name);
				}
			}
		}
		return $templates;
	}

}