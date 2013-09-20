<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockPageTypeComposerControl extends PageTypeComposerControl {

	protected $btID;
	protected $ptComposerControlTypeHandle = 'block';
	protected $bt = false;
	protected $b = false;

	public function setBlockTypeID($btID) {
		$this->btID = $btID;
		$this->setPageTypeComposerControlIdentifier($btID);
	}

	public function getBlockTypeID() {
		return $this->btID;
	}

	public function export($node) {
		$bt = $this->getBlockTypeObject();
		$node->addAttribute('handle', $bt->getBlockTypeHandle());
	}

	public function shouldPageTypeComposerControlStripEmptyValuesFromDraft() {
		return true;
	}

	public function removePageTypeComposerControlFromDraft() {
		$b = $this->getPageTypeComposerControlBlockObject($this->pDraftObject);
		$b->deleteBlock();
	}

	protected function getPageTypeComposerControlBlockObject(PageDraft $pDraft) {
		$db = Loader::db();
		if (!is_object($this->b)) {
			$setControl = $this->getPageTypeComposerFormLayoutSetControlObject();
			$c = $pDraft->getPageDraftCollectionObject();
			$r = $db->GetRow('select bID, arHandle from PageDraftBlocks where pDraftID = ? and ptComposerFormLayoutSetControlID = ?', array(
				$pDraft->getPageDraftID(), $setControl->getPageTypeComposerFormLayoutSetControlID()
			));
			if (!$r['bID']) {
				// this is the first run. so we look for the proxy block.
				$pt = PageTemplate::getByID($c->getPageTemplateID());
				$outputControl = $setControl->getPageTypeComposerOutputControlObject($pt);
				if (is_object($outputControl)) {
					$cm = $pDraft->getPageTypeObject();
					$mc = $cm->getPageTypePageTemplateDefaultPageObject($pt);
					$r = $db->GetRow('select bco.bID, cvb.arHandle from btCorePageTypeComposerControlOutput bco inner join CollectionVersionBlocks cvb on cvb.bID = bco.bID where ptComposerOutputControlID = ? and cvb.cID = ?', array(
						$outputControl->getPageTypeComposerOutputControlID(), $mc->getCollectionID()
					));
				}
			}
			if ($r['bID']) {
				$b = Block::getByID($r['bID'], $c, $r['arHandle']);
				$this->setPageTypeComposerControlBlockObject($b);
				return $this->b;
			}
		}
	}

	public function setPageTypeComposerControlBlockObject($b) {
		$this->b = $b;
	}

	public function getBlockTypeObject() {
		if (!is_object($this->bt)) {
			$this->bt = BlockType::getByID($this->btID);
		}
		return $this->bt;
	}

	public function getPageTypeComposerControlPageNameValue(Page $dc) {
		if (is_object($this->b)) {
			$controller = $this->b->getController();
			return $controller->getPageTypeComposerControlPageNameValue();
		}
	}

	public function canPageTypeComposerControlSetPageName() {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();
		if (method_exists($controller, 'getPageTypeComposerControlPageNameValue')) {
			return true;
		}
		return false;
	}

	public function isPageTypeComposerControlDraftValueEmpty() {
		$bt = $this->getBlockTypeObject();
		$controller = $bt->getController();				
		if (method_exists($controller, 'isPageTypeComposerControlDraftValueEmpty')) {
			$bx = $this->getPageTypeComposerControlBlockObject($this->pDraftObject);
			if (is_object($bx)) {
				$controller = $bx->getController();
				return $controller->isPageTypeComposerControlDraftValueEmpty();
			}
		}
		return false;
	}

	public function pageTypeComposerFormControlSupportsValidation() {
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


	public function getPageTypeComposerControlCustomTemplates() {
		$bt = $this->getBlockTypeObject();
		$txt = Loader::helper('text');
		$templates = array();
		if (is_object($bt)) {
			$blocktemplates = $bt->getBlockTypePageTypeComposerTemplates();
			if (is_array($blocktemplates)) {
				foreach($blocktemplates as $tpl) {
	                if (strpos($tpl, '.') !== false) {
	                    $name = substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
	                } else {
	                	$name = $txt->unhandle($tpl);
	                }
					$templates[] = new PageTypeComposerControlCustomTemplate($tpl, $name);
				}
			}
		}
		return $templates;
	}
	
	public function addToPageTypeComposerFormLayoutSet(PageTypeComposerFormLayoutSet $set) {
		$layoutSetControl = parent::addToPageTypeComposerFormLayoutSet($set);
		$pagetype = $set->getPageTypeObject();
		$pagetype->rescanPageTypeComposerOutputControlObjects();
		return $layoutSetControl;
	}

	public function render($label, $customTemplate) {
		$obj = $this->getPageTypeComposerControlDraftValue();
		if (!is_object($obj)) {
			$obj = $this->getBlockTypeObject();
		}

		$cnt = $obj->getController();
		$cnt->setupAndRun('composer');

		$env = Environment::get();
		$form = Loader::helper('form');
		$set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();
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
			$obj = $this->getPageTypeComposerControlDraftValue();
			if (!is_object($obj)) {
				$obj = $this->getBlockTypeObject();
			}
		}
		$controller = $obj->getController();
		extract($controller->getSets());
		extract($controller->getHelperObjects());
		$label = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerControlLabel();
		$env = Environment::get();
		include($env->getPath(DIRNAME_BLOCKS . '/' . $obj->getBlockTypeHandle() . '/' . $file));
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->pDraftObject)) {
			return $this->getPageTypeComposerControlBlockObject($this->pDraftObject);
		}
	}
	public function publishToPage(PageDraft $d, $data, $controls) {
		$c = $d->getPageDraftCollectionObject();
		// for blocks, we need to also grab their output 
		$bt = $this->getBlockTypeObject();
		$pt = PageTemplate::getByID($c->getPageTemplateID());
		$setControl = $this->getPageTypeComposerFormLayoutSetControlObject();

		$b = $this->getPageTypeComposerControlBlockObject($d);
		if (!is_object($b)) {
			return;
		}
		
		// delete the block that this set control has placed on this version, because
		// we are going to replace it with a new one.
		$db = Loader::db();
		$q = 'select cvb.arHandle, cdb.bID, cdb.cbDisplayOrder from PageDraftBlocks cdb inner join PageDrafts cd on cdb.pDraftID = cd.pDraftID inner join CollectionVersionBlocks cvb on (cdb.bID = cvb.bID and cvb.cID = cd.cID and cvb.cvID = ?) where cdb.ptComposerFormLayoutSetControlID = ? and cd.pDraftID = ?';
		$v = array($c->getVersionID(), $setControl->getPageTypeComposerFormLayoutSetControlID(), $d->getPageDraftID());
		$row = $db->GetRow($q, $v);
		if ($row['bID'] && $row['arHandle']) {
			$db->Execute('delete from PageDraftBlocks where ptComposerFormLayoutSetControlID = ? and pDraftID = ?', array($setControl->getPageTypeComposerFormLayoutSetControlID(), $d->getPageDraftID()));
		}

		$arHandle = $b->getAreaHandle();
		$blockDisplayOrder = $b->getBlockDisplayOrder();
		$b->deleteBlock();
		$ax = Area::getOrCreate($c, $arHandle);
		$b = $c->addBlock($bt, $ax, $data);
		$this->setPageTypeComposerControlBlockObject($b);
		$b->setAbsoluteBlockDisplayOrder($blockDisplayOrder);
		
		// make a reference to the new block
		$db = Loader::db();
		$db->Execute('insert into PageDraftBlocks (pDraftID, arHandle, ptComposerFormLayoutSetControlID, cbDisplayOrder, bID) values (?, ?, ?, ?, ?)', array(
			$d->getPageDraftID(), $arHandle, $setControl->getPageTypeComposerFormLayoutSetControlID(), $b->getBlockDisplayOrder(), $b->getBlockID()
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