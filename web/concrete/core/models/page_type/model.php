<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageType extends Object {

	public function getPageTypeID() {return $this->ptID;}
	public function getPageTypeName() {return $this->ptName;}
	public function getPageTypePublishTargetTypeID() {return $this->ptPublishTargetTypeID;}
	public function getPageTypePublishTargetObject() {return $this->ptPublishTargetObject;}
	public function getPageTypeAllowedPageTemplates() {
		return $this->ptAllowedPageTemplates;
	}
	public function getPageTypeDefaultPageTemplateID() {return $this->ptDefaultPageTemplateID;}
	public function getPermissionObjectIdentifier() {
		return $this->getPageTypeID();
	}

	public function getPageTypeSelectedPageTemplateObjects() {
		$templates = array();
		$db = Loader::db();
		$r = $db->Execute('select pTemplateID from PageTypePageTemplates where ptID = ? order by pTemplateID asc', array($this->ptID));
		while ($row = $r->FetchRow()) {
			$pt = PageTemplate::getByID($row['pTemplateID']);
			if (is_object($pt)) {
				$templates[] = $pt;
			}
		}
		return $templates;
	}

	public static function getByDefaultsPage(Page $c) {
		if ($c->isMasterCollection()) {
			$db = Loader::db();
			$ptID = $db->GetOne('select ptID from PageTypePageTemplateDefaultPages where cID = ?', array($c->getCollectionID()));
			if ($ptID) {
				return PageType::getByID($ptID);
			}
		}
	}

	public function getPageTypePageTemplateDefaultPageObject(PageTemplate $template) {
		$db = Loader::db();
		$cID = $db->GetOne('select cID from PageTypePageTemplateDefaultPages where ptID = ? and pTemplateID = ?', array(
			$this->ptID, $template->getPageTemplateID()
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
			$db->Execute('insert into PageTypePageTemplateDefaultPages (ptID, pTemplateID, cID) values (?, ?, ?)', array(
				$this->ptID, $template->getPageTemplateID(), $cID
			));
		}

		return Page::getByID($cID, 'RECENT');
	}

	public function getPageTypePageTemplateObjects() {
		$_templates = array();
		if ($this->ptAllowedPageTemplates == 'C') {
			$_templates = $this->getPageTypeSelectedPageTemplateObjects();
		} else {
			$templates = PageTemplate::getList();
			$db = Loader::db();
			if ($this->ptAllowedPageTemplates == 'X') {
				$_templates = array();
				$pageTemplateIDs = $db->GetCol('select pTemplateID from PageTypePageTemplates where ptID = ? order by pTemplateID asc', array($this->ptID));
				foreach($templates as $pt) {
					if (!in_array($pt->getPageTemplateID(), $pageTemplateIDs)) {
						$_templates[] = $pt;
					}
				}
			} else {
				$_templates = $templates;
			}
		}
		$defaultTemplate = PageTemplate::getByID($this->getPageTypeDefaultPageTemplateID());
		if (!in_array($defaultTemplate, $_templates)) {
			$_templates[] = $defaultTemplate;
		}
		
		return $_templates;
	}

	public static function import($node) {
		$types = array();
		if ((string) $node->pagetemplates['type'] == 'custom' || (string) $node->pagetemplates['type'] == 'except') {
			if ((string) $node->pagetemplates['type'] == 'custom') {
				$ptAllowedPageTemplates = 'C';
			} else {
				$ptAllowedPageTemplates = 'X';
			}
			
			foreach($node->pagetemplates->pagetemplate as $pagetemplate) {
				$types[] = PageTemplate::getByHandle((string) $pagetemplate['handle']);
			}
		} else {
			$ptAllowedPageTemplates = 'A';
		}
		$ptName = (string) $node['name'];
		$db = Loader::db();
		$defaultPageTemplate = PageTemplate::getByHandle((string) $node->pagetemplates['default']);

		$ptID = $db->GetOne('select ptID from PageTypes where ptName = ?', array($ptName));
		if ($ptID) {
			$cm = PageType::getByID($ptID);
			$cm->update($ptName, $defaultPageTemplate, $ptAllowedPageTemplates, $types);
		} else {
			$cm = PageType::add($ptName, $defaultPageTemplate, $ptAllowedPageTemplates, $types);
		}
		if (isset($node->target)) {
			$target = PageTypePublishTargetType::importConfiguredPageTypePublishTarget($node->target);
			$cm->setConfiguredPageTypePublishTargetObject($target);
		}
		if (isset($node->formlayout->set)) {
			foreach($node->formlayout->set as $setnode) {
				$set = $cm->addPageTypeComposerFormLayoutSet((string) $setnode['name']);
				if (isset($setnode->control)) {
					foreach($setnode->control as $controlnode) {
						$controltype = PageTypeComposerControlType::getByHandle((string) $controlnode['type']);
						$control = $controltype->configureFromImport($controlnode);
						$setcontrol = $control->addToPageTypeComposerFormLayoutSet($set);
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
							ContentImporter::addPageTypeComposerOutputControlID($setcontrol, $outputControlID);
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
					$xc = $cm->getPageTypePageTemplateDefaultPageObject($pt);
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
		$nxml = $xml->addChild('pagetypes');
		
		foreach($list as $sc) {
			$activated = 0;
			$templates = $sc->getPageTypePageTemplateObjects();
			$pagetype = $nxml->addChild('pagetype');
			$pagetype->addAttribute('name', $sc->getPageTypeName());
			$pagetemplates = $pagetype->addChild('pagetemplates');
			if ($sc->getPageTypeAllowedPageTemplates() == 'A') {
				$pagetemplates->addAttribute('type', 'all');
			} else {
				if ($sc->getPageTypeAllowedPageTemplates() == 'X') {
					$pagetemplates->addAttribute('type', 'except');
				} else {
					$pagetemplates->addAttribute('type', 'custom');
				}
				foreach($templates as $tt) {
					$pagetemplates->addChild('pagetemplate')->addAttribute('handle', $tt->getPageTemplateHandle());
				}	
			}

			$defaultPageTemplate = PageTemplate::getByID($sc->getPageTypeDefaultPageTemplateID());
			if (is_object($defaultPageTemplate)) {
				$pagetemplates->addAttribute('default', $defaultPageTemplate->getPageTemplateHandle());
			}
			$target = $sc->getPageTypePublishTargetObject();
			$target->export($pagetype);

			$fsn = $pagetype->addChild('formlayout');
			$fieldsets = PageTypeComposerFormLayoutSet::getList($sc);
			foreach($fieldsets as $fs) {
				$fs->export($fsn);
			}

			$osn = $pagetype->addChild('output');
			foreach($templates as $tt) {
				$pagetemplate = $osn->addChild('pagetemplate');
				$pagetemplate->addAttribute('handle', $tt->getPageTemplateHandle());
				$xc = $sc->getPageTypePageTemplateDefaultPageObject($tt);
				$xc->export($pagetemplate);
			}
		}
	}

	public function rescanPageTypeComposerOutputControlObjects() {
		$sets = PageTypeComposerFormLayoutSet::getList($this);
		foreach($sets as $s) {
			$controls = PageTypeComposerFormLayoutSetControl::getList($s);
			foreach($controls as $cs) {
				$type = $cs->getPageTypeComposerControlTypeObject();
				if ($type->controlTypeSupportsOutputControl()) {
					$cs->ensureOutputControlExists();
				}
			}
		}
	}

	public static function add($ptName, PageTemplate $defaultPageTemplate, $ptAllowedPageTemplates, $templates) {
		$db = Loader::db();
		$db->Execute('insert into PageTypes (ptName, ptDefaultPageTemplateID, ptAllowedPageTemplates) values (?, ?, ?)', array(
			$ptName, $defaultPageTemplate->getPageTemplateID(), $ptAllowedPageTemplates
		));
		$ptID = $db->Insert_ID();
		if ($ptAllowedPageTemplates != 'A') {
			foreach($templates as $pt) {
				$db->Execute('insert into PageTypePageTemplates (ptID, pTemplateID) values (?, ?)', array(
					$ptID, $pt->getPageTemplateID()
				));
			}
		}

		$ptt = PageType::getByID($db->Insert_ID());

		// copy permissions from the defaults to the page type
		$cpk = PermissionKey::getByHandle('access_page_type_permissions');
		$permissions = PermissionKey::getList('pagetype');
		foreach($permissions as $pk) { 
			$pk->setPermissionObject($ptt);
			$pk->copyFromDefaultsToPageType($cpk);
		}

		// now we clear the default from edit page drafts
		$pk = PermissionKey::getByHandle('edit_page_drafts_from_page_type');
		$pk->setPermissionObject($ptt);
		$pt = $pk->getPermissionAssignmentObject();
		$pt->clearPermissionAssignment();

		// now we assign the page draft owner access entity
		$pa = PermissionAccess::create($pk);
		$pe = PageDraftAuthorPermissionAccessEntity::getOrCreate();
		$pa->addListItem($pe);
		$pt->assignPermissionAccess($pa);

		return $ptt;
	}

	public function update($ptName, PageTemplate $defaultPageTemplate, $ptAllowedPageTemplates, $templates) {
		$db = Loader::db();
		$db->Execute('update PageTypes set ptName = ?, ptDefaultPageTemplateID = ?, ptAllowedPageTemplates = ? where ptID = ?', array(
			$ptName,
			$defaultPageTemplate->getPageTemplateID(),
			$ptAllowedPageTemplates,
			$this->ptID
		));
		$db->Execute('delete from PageTypePageTemplates where ptID = ?', array($this->ptID));
		if ($ptAllowedPageTemplates != 'A') {
			foreach($templates as $pt) {
				$db->Execute('insert into PageTypePageTemplates (ptID, pTemplateID) values (?, ?)', array(
					$this->ptID, $pt->getPageTemplateID()
				));
			}
		}
		$this->rescanPageTypeComposerOutputControlObjects();
	}

	public static function getList() {
		$db = Loader::db();
		$ptIDs = $db->GetCol('select ptID from PageTypes order by ptID asc');
		$list = array();
		foreach($ptIDs as $ptID) {
			$cm = PageType::getByID($ptID);
			if (is_object($cm)) {
				$list[] = $cm;
			}
		}
		return $list;
	}

	public static function getByID($ptID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from PageTypes where ptID = ?', array($ptID));
		if (is_array($r) && $r['ptID']) {
			$cm = new PageType;
			$cm->setPropertiesFromArray($r);
			$cm->ptPublishTargetObject = unserialize($r['ptPublishTargetObject']);
			return $cm;
		}
	}

	public function delete() {
		$sets = PageTypeComposerFormLayoutSet::getList($this);
		foreach($sets as $set) {
			$set->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from PageTypes where ptID = ?', array($this->ptID));
		$db->Execute('delete from PageTypePageTemplates where ptID = ?', array($this->ptID));
		$db->Execute('delete from PageTypeComposerOutputControls where ptID = ?', array($this->ptID));
	}

	public function setConfiguredPageTypePublishTargetObject(PageTypePublishTargetConfiguration $configuredTarget) {
		$db = Loader::db();
		if (is_object($configuredTarget)) {
			$db->Execute('update PageTypes set ptPublishTargetTypeID = ?, ptPublishTargetObject = ? where ptID = ?', array(
				$configuredTarget->getPageTypePublishTargetTypeID(),
				@serialize($configuredTarget),
				$this->getPageTypeID()
			));
		}
	}

	public function rescanFormLayoutSetDisplayOrder() {
		$sets = PageTypeComposerFormLayoutSet::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}

	public function addPageTypeComposerFormLayoutSet($ptComposerFormLayoutSetName) {
		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(ptComposerFormLayoutSetID) from PageTypeComposerFormLayoutSets where ptID = ?', array($this->ptID));
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$db->Execute('insert into PageTypeComposerFormLayoutSets (ptComposerFormLayoutSetName, ptID, ptComposerFormLayoutSetDisplayOrder) values (?, ?, ?)', array(
			$ptComposerFormLayoutSetName, $this->ptID, $displayOrder
		));	
		return PageTypeComposerFormLayoutSet::getByID($db->Insert_ID());
	}

	public function validateCreateDraftRequest($pt) {
		$e = Loader::helper('validation/error');
		$availablePageTemplates = $this->getPageTypePageTemplateObjects();
		$availablePageTemplateIDs = array();
		foreach($availablePageTemplates as $ppt) {
			$availablePageTemplateIDs[] = $ppt->getPageTemplateID();
		}
		if (!is_object($pt)) {
			$e->add(t('You must choose a page template.'));
		} else if (!in_array($pt->getPageTemplateID(), $availablePageTemplateIDs)) {
			$e->add(t('This page template is not a valid template for this page type.'));
		}
		return $e;
	}	

	/** 
	 * Validates an entire request, from create draft, individual controls, and publish location. Useful for front-end forms that make use of composer without
	 * interim steps like autosave
	 */
	public function validatePublishRequest($pt, $parent) {
		$e = $this->validateCreateDraftRequest($pt);

		$controls = PageTypeComposerControl::getList($this);
		$outputControls = array();
		foreach($controls as $cn) {
			$data = $cn->getRequestValue();
			if ($cn->isPageTypeComposerFormControlRequiredOnThisRequest()) {
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
		$ptID = $this->getPageTypeID();
		$pDraftDateCreated = Loader::helper('date')->getSystemDateTime();
		$uID = $u->getUserID();
		
		$db->Execute('insert into PageDrafts (ptID, pDraftDateCreated, uID) values (?, ?, ?)', array(
			$ptID, $pDraftDateCreated, $uID
		));	

		$pDraftID = $db->Insert_ID();

		$parent = Page::getByPath(PAGE_DRAFTS_PAGE_PATH);
		$data = array('cvIsApproved' => 0);
		$p = $parent->add($this, $data, $pt);
		$p->deactivate();

		$db->Execute('update PageDrafts set cID = ? where pDraftID = ?', array($p->getCollectionID(), $pDraftID));
		$d = PageDraft::getByID($pDraftID);

		PageDraftAuthorPermissionAccessEntity::refresh($d);

		return $d;
	}

}