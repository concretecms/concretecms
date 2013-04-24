<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Composer extends Object {

	public function getComposerID() {return $this->cmpID;}
	public function getComposerName() {return $this->cmpName;}
	public function getComposerTargetTypeID() {return $this->cmpTargetTypeID;}
	public function getComposerTargetObject() {return $this->cmpTargetObject;}
	public function getComposerAllowedPageTypes() {
		return $this->cmpAllowedPageTypes;
	}

	public function getPermissionObjectIdentifier() {
		return $this->getComposerID();
	}

	public function getComposerFormSelectedPageTypeObjects() {
		$types = array();
		$db = Loader::db();
		$r = $db->Execute('select ctID from ComposerPageTypes where cmpID = ? order by ctID asc', array($this->cmpID));
		while ($row = $r->FetchRow()) {
			$ct = CollectionType::getByID($row['ctID']);
			if (is_object($ct)) {
				$types[] = $ct;
			}
		}
		return $types;
	}

	public function getComposerPageTypeObjects() {
		if ($this->cmpAllowedPageTypes == 'C') {
			return $this->getComposerFormSelectedPageTypeObjects();
		} else {
			$pagetypes = CollectionType::getList();
			$db = Loader::db();
			if ($this->cmpAllowedPageTypes == 'X') {
				$_pagetypes = array();
				$ctIDs = $db->GetCol('select ctID from ComposerPageTypes where cmpID = ? order by ctID asc', array($this->cmpID));
				foreach($pagetypes as $ct) {
					if (!in_array($ct->getCollectionTypeID(), $ctIDs)) {
						$_pagetypes[] = $ct;
					}
				}
				return $_pagetypes;
			}
			return $pagetypes;

		}
	}

	public static function import($node) {
		$types = array();
		if ((string) $node->pagetypes['type'] == 'custom' || (string) $node->pagetypes['type'] == 'except') {
			$cmpAllowedPageTypes = 'C';
			foreach($node->pagetypes->pagetype as $pagetype) {
				$types[] = CollectionType::getByHandle((string) $pagetype['handle']);
			}
		} else {
			$cmpAllowedPageTypes = 'A';
		}
		$cm = Composer::add((string) $node['name'], $cmpAllowedPageTypes, $types);
		$target = ComposerTargetType::importConfiguredComposerTarget($node->target);
		$cm->setConfiguredComposerTargetObject($target);

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

		if (isset($node->output->pagetype)) {
			foreach($node->output->pagetype as $pagetype) {
				foreach($pagetype->area as $area) {
					$displayOrder = 0;
					foreach($area->control as $outputcontrolnode) {
						$ct = CollectionType::getByHandle((string) $pagetype['handle']);
						$formLayoutSetControlID = ContentImporter::getComposerFormLayoutSetControlFromTemporaryID((string) $outputcontrolnode['output-control-id']);
						$formLayoutSetControl = ComposerFormLayoutSetControl::getByID($formLayoutSetControlID);
						$outputControl = ComposerOutputControl::getByComposerFormLayoutSetControl($ct, $formLayoutSetControl);

						$outputControl->updateComposerOutputControlArea((string) $area['name']);
						$outputControl->updateComposerOutputControlDisplayOrder($displayOrder);
						$displayOrder++;
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
			$types = $sc->getComposerPageTypeObjects();

			$composer = $nxml->addChild('composer');
			$composer->addAttribute('name', $sc->getComposerName());
			$pagetypes = $composer->addChild('pagetypes');
			if ($sc->getComposerAllowedPageTypes() == 'A') {
				$pagetypes->addAttribute('type', 'all');
			} else {
				if ($sc->getComposerAllowedPageTypes() == 'X') {
					$pagetypes->addAttribute('type', 'except');
				} else {
					$pagetypes->addAttribute('type', 'custom');
				}
				foreach($types as $ct) {
					$pagetypes->addChild('pagetype')->addAttribute('handle', $ct->getCollectionTypeHandle());
				}	
			}
			$target = $sc->getComposerTargetObject();
			$target->export($composer);

			$fsn = $composer->addChild('formlayout');
			$fieldsets = ComposerFormLayoutSet::getList($sc);
			foreach($fieldsets as $fs) {
				$fs->export($fsn);
			}

			$osn = $composer->addChild('output');
			foreach($types as $ct) {
				$pagetype = $osn->addChild('pagetype');
				$pagetype->addAttribute('handle', $ct->getCollectionTypeHandle());
				$areas = ComposerOutputControl::getCollectionTypeAreas($ct);
				foreach($areas as $arHandle) {
					$area = $pagetype->addChild('area');
					$area->addAttribute('name', $arHandle);
					$controls = ComposerOutputControl::getList($sc, $ct, $arHandle);
					foreach($controls as $outputControl) {
						$outputControl->export($area);
					}
				}

			}
		}
	}

	public static function add($cmpName, $cmpAllowedPageTypes, $types) {
		$db = Loader::db();
		$db->Execute('insert into Composers (cmpName, cmpAllowedPageTypes) values (?, ?)', array(
			$cmpName, $cmpAllowedPageTypes
		));
		$cmpID = $db->Insert_ID();
		if ($cmpAllowedPageTypes != 'A') {
			foreach($types as $ct) {
				$db->Execute('insert into ComposerPageTypes (cmpID, ctID) values (?, ?)', array(
					$cmpID, $ct->getCollectionTypeID()
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

	public function update($cmpName, $cmpAllowedPageTypes, $types) {
		$db = Loader::db();
		$db->Execute('update Composers set cmpName = ?, cmpAllowedPageTypes = ? where cmpID = ?', array(
			$cmpName,
			$cmpAllowedPageTypes,
			$this->cmpID
		));
		$db->Execute('delete from ComposerPageTypes where cmpID = ?', array($this->cmpID));
		if ($cmpAllowedPageTypes != 'A') {
			foreach($types as $ct) {
				$db->Execute('insert into ComposerPageTypes (cmpID, ctID) values (?, ?)', array(
					$this->cmpID, $ct->getCollectionTypeID()
				));
			}
		}
	}

	public static function getList() {
		$db = Loader::db();
		$cmpIDs = $db->GetCol('select cmpID from Composers order by cmpName asc');
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
		$db->Execute('delete from ComposerPageTypes where cmpID = ?', array($this->cmpID));
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

	public function validateCreateDraftRequest($ct) {
		$e = Loader::helper('validation/error');
		$availablePageTypes = $this->getComposerPageTypeObjects();
		$availablePageTypeIDs = array();
		foreach($availablePageTypes as $cct) {
			$availablePageTypeIDs[] = $cct->getCollectionTypeID();
		}
		if (!is_object($ct)) {
			$e->add(t('You must choose a page type.'));
		} else if (!in_array($ct->getCollectionTypeID(), $availablePageTypeIDs)) {
			$e->add(t('This page type is not a valid page type for this composer.'));
		}
		return $e;
	}	

	/** 
	 * Validates an entire request, from create draft, individual controls, and publish location. Useful for front-end forms that make use of composer without
	 * interim steps like autosave
	 */
	public function validatePublishRequest($ct, $parent) {
		$e = $this->validateCreateDraftRequest($ct);

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

	public function createDraft(CollectionType $ct, $u = false) {
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
		$p = $parent->add($ct, $data);
		$p->deactivate();

		$db->Execute('update ComposerDrafts set cID = ? where cmpDraftID = ?', array($p->getCollectionID(), $cmpDraftID));
		$cmp = ComposerDraft::getByID($cmpDraftID);

		ComposerDraftAuthorPermissionAccessEntity::refresh($cmp);

		return $cmp;
	}

}