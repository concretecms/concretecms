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

	public function export($node) {
		$bt = $this->getBlockTypeObject();
		$node->addAttribute('handle', $bt->getBlockTypeHandle());
	}

	public function shouldComposerControlStripEmptyValuesFromDraft() {
		return true;
	}

	public function removeComposerControlFromDraft() {
		$b = $this->getComposerControlBlockObject($this->cmpDraftObject);
		$b->deleteBlock();
	}

	protected function getComposerControlBlockObject(ComposerDraft $cmpDraft) {
		$db = Loader::db();
		if (!is_object($this->b)) {
			$setControl = $this->getComposerFormLayoutSetControlObject();
			$c = $cmpDraft->getComposerDraftCollectionObject();
			$r = $db->GetRow('select bID, arHandle from ComposerDraftBlocks where cmpDraftID = ? and cmpFormLayoutSetControlID = ?', array(
				$cmpDraft->getComposerDraftID(), $setControl->getComposerFormLayoutSetControlID()
			));
			if (!$r['bID']) {
				// this is the first run. so we look for the proxy block.
				$pt = PageTemplate::getByID($c->getPageTemplateID());
				$outputControl = $setControl->getComposerOutputControlObject($pt);
				$cm = $cmpDraft->getComposerObject();
				$mc = $cm->getComposerPageTemplateDefaultPageObject($pt);
				$r = $db->GetRow('select bco.bID, cvb.arHandle from btCoreComposerControlOutput bco inner join CollectionVersionBlocks cvb on cvb.bID = bco.bID where cmpOutputControlID = ? and cvb.cID = ?', array(
					$outputControl->getComposerOutputControlID(), $mc->getCollectionID()
				));
			}
			if ($r['bID']) {
				$b = Block::getByID($r['bID'], $c, $r['arHandle']);
				$this->setComposerControlBlockObject($b);
				return $this->b;
			}
		}
	}

	public function setComposerControlBlockObject($b) {
		$this->b = $b;
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

	public function isComposerControlDraftValueEmpty() {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();				
		if (method_exists($controller, 'isComposerControlDraftValueEmpty')) {
			$bx = $this->getComposerControlBlockObject($this->cmpDraftObject);
			if (is_object($bx)) {
				$controller = $bx->getController();
				return $controller->isComposerControlDraftValueEmpty();
			}
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

	public function addAssetsToRequest(Controller $cnt) {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		$controller->setupAndRun('composer');
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
		$composer = $set->getComposerObject();
		$composer->rescanComposerOutputControlObjects();
		return $layoutSetControl;
	}

	public function render($label, $customTemplate) {
		$obj = $this->getComposerControlDraftValue();
		if (!is_object($obj)) {
			$obj = $this->getBlockTypeObject();
		}

		$cnt = $obj->getController();
		$cnt->setupAndRun('composer');

		$env = Environment::get();
		$form = Loader::helper('form');
		$set = $this->getComposerFormLayoutSetControlObject()->getComposerFormLayoutSetObject();
		$control = $this;

		if ($customTemplate) {
			$rec = $env->getRecord(DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate);
			if ($rec->exists()) {
				$template = DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate;
			}
		}

		if (!isset($template)) {
			$template = FILENAME_BLOCK_COMPOSER;
		}

		$this->inc($template, array('control' => $this, 'obj' => $obj));
	}

	public function inc($file, $args = array()) {
		extract($args);
		if (!isset($obj)) {
			$obj = $this->getComposerControlDraftValue();
			if (!is_object($obj)) {
				$obj = $this->getBlockTypeObject();
			}
		}
		$controller = $obj->getController();
		extract($controller->getSets());
		extract($controller->getHelperObjects());
		$label = $this->getComposerFormLayoutSetControlObject()->getComposerControlLabel();
		$env = Environment::get();
		include($env->getPath(DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $file));
	}

	public function getComposerControlDraftValue() {
		if (is_object($this->cmpDraftObject)) {
			return $this->getComposerControlBlockObject($this->cmpDraftObject);
		}
	}
	public function publishToPage(ComposerDraft $d, $data, $controls) {
		$c = $d->getComposerDraftCollectionObject();
		// for blocks, we need to also grab their output 
		$bt = $this->getBlockTypeObject();
		$pt = PageTemplate::getByID($c->getPageTemplateID());
		$setControl = $this->getComposerFormLayoutSetControlObject();

		$b = $this->getComposerControlBlockObject($d);
		// delete the block that this set control has placed on this version, because
		// we are going to replace it with a new one.
		$db = Loader::db();
		$q = 'select cvb.arHandle, cdb.bID from ComposerDraftBlocks cdb inner join ComposerDrafts cd on cdb.cmpDraftID = cd.cmpDraftID inner join CollectionVersionBlocks cvb on (cdb.bID = cvb.bID and cvb.cID = cd.cID and cvb.cvID = ?) where cdb.cmpFormLayoutSetControlID = ? and cd.cmpDraftID = ?';
		$v = array($c->getVersionID(), $setControl->getComposerFormLayoutSetControlID(), $d->getComposerDraftID());
		$row = $db->GetRow($q, $v);
		if ($row['bID'] && $row['arHandle']) {
			$db->Execute('delete from ComposerDraftBlocks where cmpFormLayoutSetControlID = ? and cmpDraftID = ?', array($setControl->getComposerFormLayoutSetControlID(), $d->getComposerDraftID()));
		}

		$arHandle = $b->getAreaHandle();
		$b->deleteBlock();
		$ax = Area::getOrCreate($c, $arHandle);
		$b = $c->addBlock($bt, $ax, $data);
		$this->setComposerControlBlockObject($b);

		// make a reference to the new block
		$db = Loader::db();
		$db->Execute('insert into ComposerDraftBlocks (cmpDraftID, arHandle, cmpFormLayoutSetControlID, bID) values (?, ?, ?, ?)', array(
			$d->getComposerDraftID(), $arHandle, $setControl->getComposerFormLayoutSetControlID(), $b->getBlockID()
		));
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