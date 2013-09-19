<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Composer extends Object {

	public function getComposerID() {return $this->cmpID;}
	public function getComposerName() {return $this->cmpName;}
	public function getComposerTargetTypeID() {return $this->cmpTargetTypeID;}
	public function getComposerTargetObject() {return $this->cmpTargetObject;}
	public function getComposerAllowedPageTemplates() {
		return $this->cmpAllowedPageTemplates;
	}
	public function getComposerDefaultPageTemplateID() {return $this->cmpDefaultPageTemplateID;}
	public function getPermissionObjectIdentifier() {
		return $this->getComposerID();
	}

	public function getComposerFormSelectedPageTemplateObjects() {
		$templates = array();
		$db = Loader::db();
		$r = $db->Execute('select pTemplateID from ComposerPageTemplates where cmpID = ? order by pTemplateID asc', array($this->cmpID));
		while ($row = $r->FetchRow()) {
			$pt = PageTemplate::getByID($row['pTemplateID']);
			if (is_object($pt)) {
				$templates[] = $pt;
			}
		}
		return $templates;
	}

	public function getComposerPageTemplateDefaultPageObject(PageTemplate $template) {
		$db = Loader::db();
		$cID = $db->GetOne('select cID from ComposerPageTemplateDefaultPages where cmpID = ? and pTemplateID = ?', array(
			$this->cmpID, $template->getPageTemplateID()
		));
		if (!$cID) {
			// we create one.
			$dh = Loader::helper('date');
			$cDate = $dh->getSystemDateTime();
			$data['pTemplateID'] = $template->getPageTemplateID();
			$cobj = Collection::add($data);
			$cID = $cobj->getCollectionID();
			
			$v2 = array($cID, 1);
			$q2 = "insert into Pages (cID, cIsTemplate) values (?, ?)";
			$r2 = $db->prepare($q2);
			$res2 = $db->execute($r2, $v2);

			$cID = $db->Insert_ID();
			$db->Execute('insert into ComposerPageTemplateDefaultPages (cmpID, pTemplateID, cID) values (?, ?, ?)', array(
				$this->cmpID, $template->getPageTemplateID(), $cID
			));
		}

		return Page::getByID($cID, 'RECENT');
	}

	public function refreshComposerOutputAreaList() {
		$db = Loader::db();
		$templates = $this->getComposerPageTemplateObjects();
		$db->Execute('delete from ComposerOutputAreas where cmpID = ?', array($this->cmpID));
		foreach($templates as $pt) {
			$areaHandles = $pt->getPageTemplateAreaHandles();
			foreach($areaHandles as $arHandle) {
				$db->Execute('insert into ComposerOutputAreas (cmpID, pTemplateID, arHandle) values (?, ?, ?)', array(
					$this->cmpID, $pt->getPageTemplateID(), $arHandle
				));
			}
		}
	}

	public function getComposerPageTemplateObjects() {
		$_templates = array();
		if ($this->cmpAllowedPageTemplates == 'C') {
			$_templates = $this->getComposerFormSelectedPageTemplateObjects();
		} else {
			$templates = PageTemplate::getList();
			$db = Loader::db();
			if ($this->cmpAllowedPageTemplates == 'X') {
				$_templates = array();
				$pageTemplateIDs = $db->GetCol('select pTemplateID from ComposerPageTemplates where cmpID = ? order by pTemplateID asc', array($this->cmpID));
				foreach($templates as $pt) {
					if (!in_array($pt->getPageTemplateID(), $pageTemplateIDs)) {
						$_templates[] = $pt;
					}
				}
			} else {
				$_templates = $templates;
			}
		}
		$defaultTemplate = PageTemplate::getByID($this->getComposerDefaultPageTemplateID());
		if (!in_array($defaultTemplate, $_templates)) {
			$_templates[] = $defaultTemplate;
		}
		
		return $_templates;
	}

	public static function import($node) {
		$types = array();
		if ((string) $node->pagetemplates['type'] == 'custom' || (string) $node->pagetemplates['type'] == 'except') {
			if ((string) $node->pagetemplates['type'] == 'custom') {
				$cmpAllowedPageTemplates = 'C';
			} else {
				$cmpAllowedPageTemplates = 'X';
			}
			
			foreach($node->pagetemplates->pagetemplate as $pagetemplate) {
				$types[] = PageTemplate::getByHandle((string) $pagetemplate['handle']);
			}
		} else {
			$cmpAllowedPageTemplates = 'A';
		}
		$cmpName = (string) $node['name'];
		$db = Loader::db();
		$defaultPageTemplate = PageTemplate::getByHandle((string) $node->pagetemplates['default']);

		$cmpID = $db->GetOne('select cmpID from Composers where cmpName = ?', array($cmpName));
		if ($cmpID) {
			$cm = Composer::getByID($cmpID);
			$cm->update($cmpName, $defaultPageTemplate, $cmpAllowedPageTemplates, $types);
		} else {
			$cm = Composer::add($cmpName, $defaultPageTemplate, $cmpAllowedPageTemplates, $types);
		}
		if (isset($node->target)) {
			$target = ComposerTargetType::importConfiguredComposerTarget($node->target);
			$cm->setConfiguredComposerTargetObject($target);
		}
		if (isset($node->formlayout->set)) {
			foreach($node->formlayout->set as $setnode) {
				$set = $cm->addComposerFormLayoutSet((string) $setnode['name']);
				if (isset($setnode->control)) {
					foreach($setnode->control as $controlnode) {
						$controltype = ComposerControlType::getByHandle((string) $controlnode['type']);
						$control = $controltype->configureFromImport($controlnode);
						$setcontrol = $control->addToComposerFormLayoutSet($set);
						$required = (string) $controlnode['required'];
						$customTemplate = (string) $controlnode['custom-template'];
						$label = (string) $controlnode['custom-label'];
						$outputControlID = (string) $controlnode['output-control-id'];
						if ($required == '1') {
							$setcontrol->updateFormLayoutSetControlRequired(true);
						} else {
							$setcontrol->updateFormLayoutSetControlRequired(false);
						}
						if ($customTemplate) {
							$setcontrol->updateFormLayoutSetControlCustomTemplate($customTemplate);
						}
						if ($label) {
							$setcontrol->updateFormLayoutSetControlCustomLabel($label);
						}
						if ($outputControlID) {
							ContentImporter::addComposerOutputControlID($setcontrol, $outputControlID);
						}
					}
				}
			}
		}

		if (isset($node->output->pagetemplate)) {
			$ci = new ContentImporter();
			foreach($node->output->pagetemplate as $pagetemplate) {
				$pt = PageTemplate::getByHandle((string) $pagetemplate['handle']);
				if (is_object($pt)) {
					// let's get the defaults page for this
					$xc = $cm->getComposerPageTemplateDefaultPageObject($pt);
					// now that we have the defaults page, let's import this content into it.
					if (isset($pagetemplate->page)) {
						$ci->importPageAreas($xc, $pagetemplate->page);
					}
				}
			}
		}
	}



	public static function exportList($xml) {
		$list = self::getList();
		$nxml = $xml->addChild('composers');
		
		foreach($list as $sc) {
			$activated = 0;
			$templates = $sc->getComposerPageTemplateObjects();
			$composer = $nxml->addChild('composer');
			$composer->addAttribute('name', $sc->getComposerName());
			$pagetemplates = $composer->addChild('pagetemplates');
			if ($sc->getComposerAllowedPageTemplates() == 'A') {
				$pagetemplates->addAttribute('type', 'all');
			} else {
				if ($sc->getComposerAllowedPageTemplates() == 'X') {
					$pagetemplates->addAttribute('type', 'except');
				} else {
					$pagetemplates->addAttribute('type', 'custom');
				}
				foreach($templates as $tt) {
					$pagetemplates->addChild('pagetemplate')->addAttribute('handle', $tt->getPageTemplateHandle());
				}	
			}

			$defaultPageTemplate = PageTemplate::getByID($sc->getComposerDefaultPageTemplateID());
			if (is_object($defaultPageTemplate)) {
				$pagetemplates->addAttribute('default', $defaultPageTemplate->getPageTemplateHandle());
			}
			$target = $sc->getComposerTargetObject();
			$target->export($composer);

			$fsn = $composer->addChild('formlayout');
			$fieldsets = ComposerFormLayoutSet::getList($sc);
			foreach($fieldsets as $fs) {
				$fs->export($fsn);
			}

			$osn = $composer->addChild('output');
			foreach($templates as $tt) {
				$pagetemplate = $osn->addChild('pagetemplate');
				$pagetemplate->addAttribute('handle', $tt->getPageTemplateHandle());
				$xc = $sc->getComposerPageTemplateDefaultPageObject($tt);
				$xc->export($pagetemplate);
			}
		}
	}

	public function rescanComposerOutputControlObjects() {
		$sets = ComposerFormLayoutSet::getList($this);
		foreach($sets as $s) {
			$controls = ComposerFormLayoutSetControl::getList($s);
			foreach($controls as $cs) {
				$type = $cs->getComposerControlTypeObject();
				if ($type->controlTypeSupportsOutputControl()) {
					$cs->ensureOutputControlExists();
				}
			}
		}
	}

	public static function add($cmpName, PageTemplate $defaultPageTemplate, $cmpAllowedPageTemplates, $templates) {
		$db = Loader::db();
		$db->Execute('insert into Composers (cmpName, cmpDefaultPageTemplateID, cmpAllowedPageTemplates) values (?, ?, ?)', array(
			$cmpName, $defaultPageTemplate->getPageTemplateID(), $cmpAllowedPageTemplates
		));
		$cmpID = $db->Insert_ID();
		if ($cmpAllowedPageTemplates != 'A') {
			foreach($templates as $pt) {
				$db->Execute('insert into ComposerPageTemplates (cmpID, pTemplateID) values (?, ?)', array(
					$cmpID, $pt->getPageTemplateID()
				));
			}
		}

		$cmp = Composer::getByID($db->Insert_ID());

		// copy permissions from the defaults to the composer
		$cpk = PermissionKey::getByHandle('access_composer_permissions');
		$permissions = PermissionKey::getList('composer');
		foreach($permissions as $pk) { 
			$pk->setPermissionObject($cmp);
			$pk->copyFromDefaultsToComposer($cpk);
		}

		// now we clear the default from edit composer drafts
		$pk = PermissionKey::getByHandle('edit_composer_drafts_from_composer');
		$pk->setPermissionObject($cmp);
		$pt = $pk->getPermissionAssignmentObject();
		$pt->clearPermissionAssignment();

		// now we assign the composer draft owner access entity
		$pa = PermissionAccess::create($pk);
		$pe = ComposerDraftAuthorPermissionAccessEntity::getOrCreate();
		$pa->addListItem($pe);
		$pt->assignPermissionAccess($pa);

		return $cmp;
	}

	public function update($cmpName, PageTemplate $defaultPageTemplate, $cmpAllowedPageTemplates, $templates) {
		$db = Loader::db();
		$db->Execute('update Composers set cmpName = ?, cmpDefaultPageTemplateID = ?, cmpAllowedPageTemplates = ? where cmpID = ?', array(
			$cmpName,
			$defaultPageTemplate->getPageTemplateID(),
			$cmpAllowedPageTemplates,
			$this->cmpID
		));
		$db->Execute('delete from ComposerPageTemplates where cmpID = ?', array($this->cmpID));
		if ($cmpAllowedPageTemplates != 'A') {
			foreach($templates as $pt) {
				$db->Execute('insert into ComposerPageTemplates (cmpID, pTemplateID) values (?, ?)', array(
					$this->cmpID, $pt->getPageTemplateID()
				));
			}
		}
		$this->rescanComposerOutputControlObjects();
	}

	public static function getList() {
		$db = Loader::db();
		$cmpIDs = $db->GetCol('select cmpID from Composers order by cmpID asc');
		$list = array();
		foreach($cmpIDs as $cmpID) {
			$cm = Composer::getByID($cmpID);
			if (is_object($cm)) {
				$list[] = $cm;
			}
		}
		return $list;
	}

	public static function getByID($cmpID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from Composers where cmpID = ?', array($cmpID));
		if (is_array($r) && $r['cmpID']) {
			$cm = new Composer;
			$cm->setPropertiesFromArray($r);
			$cm->cmpTargetObject = unserialize($r['cmpTargetObject']);
			return $cm;
		}
	}

	public function delete() {
		$sets = ComposerFormLayoutSet::getList($this);
		foreach($sets as $set) {
			$set->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from Composers where cmpID = ?', array($this->cmpID));
		$db->Execute('delete from ComposerPageTemplates where cmpID = ?', array($this->cmpID));
		$db->Execute('delete from ComposerOutputControls where cmpID = ?', array($this->cmpID));
	}

	public function setConfiguredComposerTargetObject(ComposerTargetConfiguration $configuredTarget) {
		$db = Loader::db();
		if (is_object($configuredTarget)) {
			$db->Execute('update Composers set cmpTargetTypeID = ?, cmpTargetObject = ? where cmpID = ?', array(
				$configuredTarget->getComposerTargetTypeID(),
				@serialize($configuredTarget),
				$this->getComposerID()
			));
		}
	}

	public function rescanFormLayoutSetDisplayOrder() {
		$sets = ComposerFormLayoutSet::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}

	public function addComposerFormLayoutSet($cmpFormLayoutSetName) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpFormLayoutSetID) from ComposerFormLayoutSets where cmpID = ?', array($this->cmpID));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$db->Execute('insert into ComposerFormLayoutSets (cmpFormLayoutSetName, cmpID, cmpFormLayoutSetDisplayOrder) values (?, ?, ?)', array(
			$cmpFormLayoutSetName, $this->cmpID, $displayOrder
		));	
		return ComposerFormLayoutSet::getByID($db->Insert_ID());
	}

	public function validateCreateDraftRequest($pt) {
		$e = Loader::helper('validation/error');
		$availablePageTemplates = $this->getComposerPageTemplateObjects();
		$availablePageTemplateIDs = array();
		foreach($availablePageTemplates as $ppt) {
			$availablePageTemplateIDs[] = $ppt->getPageTemplateID();
		}
		if (!is_object($pt)) {
			$e->add(t('You must choose a page template.'));
		} else if (!in_array($pt->getPageTemplateID(), $availablePageTemplateIDs)) {
			$e->add(t('This page type is not a valid page type for this composer.'));
		}
		return $e;
	}	

	/** 
	 * Validates an entire request, from create draft, individual controls, and publish location. Useful for front-end forms that make use of composer without
	 * interim steps like autosave
	 */
	public function validatePublishRequest($pt, $parent) {
		$e = $this->validateCreateDraftRequest($pt);

		$controls = ComposerControl::getList($this);
		$outputControls = array();
		foreach($controls as $cn) {
			$data = $cn->getRequestValue();
			if ($cn->isComposerFormControlRequiredOnThisRequest()) {
				$cn->validate($data, $e);
			}
		}

		if (!is_object($parent) || $parent->isError()) {
			$e->add(t('You must choose a page to publish this page beneath.'));
		}

		return $e;
	}

	public function createDraft(PageTemplate $pt, $u = false) {
		if (!is_object($u)) {
			$u = new User();
		}
		$db = Loader::db();
		$cmpID = $this->getComposerID();
		$cmpDateCreated = Loader::helper('date')->getSystemDateTime();
		$uID = $u->getUserID();
		
		$db->Execute('insert into ComposerDrafts (cmpID, cmpDateCreated, uID) values (?, ?, ?)', array(
			$cmpID, $cmpDateCreated, $uID
		));	

		$cmpDraftID = $db->Insert_ID();

		$parent = Page::getByPath(COMPOSER_DRAFTS_PAGE_PATH);
		$data = array('cvIsApproved' => 0);
		$p = $parent->add($this, $data, $pt);
		$p->deactivate();

		$db->Execute('update ComposerDrafts set cID = ? where cmpDraftID = ?', array($p->getCollectionID(), $cmpDraftID));
		$cmp = ComposerDraft::getByID($cmpDraftID);

		ComposerDraftAuthorPermissionAccessEntity::refresh($cmp);

		return $cmp;
	}

}