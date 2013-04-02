<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockComposerControl extends ComposerControl {

	protected $btID;
	protected $cmpControlTypeHandle = 'block';
	protected $bt = false;
	protected $b = false;

	public function setBlockTypeID($btID) {
		$this->btID = $btID;
		$this->setComposerControlIdentifier($btID);
	}

	public function getBlockTypeID() {
		return $this->btID;
	}

	public function setComposerControlBlockObject($b) {
		$this->b = $b;
	}

	public function onComposerControlRender() {
		$bt = $this->getBlockTypeObject();
		$cnt = $bt->getController();
		$cnt->setupAndRun('composer');
	}

	public function getBlockTypeObject() {
		if (!is_object($this->bt)) {
			$this->bt = BlockType::getByID($this->btID);
		}
		return $this->bt;
	}

	public function getComposerControlPageNameValue(Page $dc) {
		if (is_object($this->b)) {
			$controller = $this->b->getController();
			return $controller->getComposerControlPageNameValue();
		}
	}

	public function canComposerControlSetPageName() {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		if (method_exists($controller, 'getComposerControlPageNameValue')) {
			return true;
		}
		return false;
	}

	public function composerFormControlSupportsValidation() {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		if (method_exists($controller, 'validate_composer')) {
			return true;
		}
		return false;
	}


	public function getComposerControlCustomTemplates() {
		$bt = $this->getBlockTypeObject();
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

	public function addToComposerFormLayoutSet(ComposerFormLayoutSet $set) {
		$layoutSetControl = parent::addToComposerFormLayoutSet($set);
		ComposerOutputControl::add($layoutSetControl, STACKS_AREA_NAME);
		return $layoutSetControl;
	}

	public function render($label, $customTemplate) {
		$env = Environment::get();
		$form = Loader::helper('form');
		$bt = $this->getBlockTypeObject();
		if ($customTemplate) {
			$rec = $env->getRecord(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate);
			if ($rec->exists()) {
				$template = $customTemplate;
			}
		}

		if (!isset($template)) {
			$template = FILENAME_BLOCK_COMPOSER;
		}

		$this->inc($template);
	}

	public function inc($file, $args = array()) {
		extract($args);
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		extract($controller->getSets());
		extract($controller->getHelperObjects());
		$label = $this->getComposerFormLayoutSetControlObject()->getComposerControlLabel();
		$env = Environment::get();
		include($env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . $file));
	}

	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$c = $d->getComposerDraftCollectionObject();
		// for blocks, we need to also grab their output 
		$bt = $this->getBlockTypeObject();
		$outputControl = $this->getComposerFormLayoutSetControlObject()->getComposerOutputControlObject();
		$arHandle = $outputControl->getComposerOutputControlAreaHandle();
		$ax = Area::getOrCreate($c, $arHandle);
		$b = $c->addBlock($bt, $ax, $data);
		$this->setComposerControlBlockObject($b);
	}

	public function validate($data, ValidationErrorHelper $e) {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		if (method_exists($controller, 'validate_composer')) {
			$e1 = $controller->validate_composer($data);
		}
		if (is_object($e1)) {
			$e->add($e1);
		}
	}


}