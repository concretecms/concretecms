<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($cmpID = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!is_object($this->composer)) {
			$composers = Composer::getList();
			if (count($composers) == 1) {
				$cmp = $composers[0];
				$this->redirect('/dashboard/composer/write', $cmp->getComposerID());
			} else {
				$this->set('composers', $composers);
			}
		} else {
			$this->set('composer', $this->composer);
			$this->set('fieldsets', ComposerFormLayoutSet::getList($this->composer));
			$this->setupAssets();
		}
	}

	protected function setupAssets() {
		$sets = $this->get('fieldsets');
		foreach($sets as $s) {
			$controls = ComposerFormLayoutSetControl::getList($s);
			foreach($controls as $cn) {
				$basecontrol = $cn->getComposerControlObject();
				$basecontrol->onComposerControlRender();
			}
		}
	}

	protected function publish(ComposerDraft $d, $outputControls) {

		if (!$d->getComposerDraftTargetParentPageID()) {
			$this->error->add(t('You must choose a page to publish this page beneath.'));
		}

		foreach($outputControls as $oc) {
			if ($oc->isComposerFormControlRequiredOnThisRequest()) {
				$data = $oc->getRequestValue();
				$oc->validate($data, $this->error);
			}
		}

		if (!$this->error->has()) {
			$d->publish();
			$c = $d->getComposerDraftCollectionObject();
			header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		}
	}

	public function save($cmpID = false) {
		Cache::disableCache();
		Cache::disableLocalCache();
		session_write_close();

		$this->view($cmpID);
		$ct = CollectionType::getByID($this->post('cmpPageTypeID'));
		$availablePageTypes = $this->composer->getComposerPageTypeObjects();

		if (!is_object($ct) && count($availablePageTypes) == 1) {
			$ct = $availablePageTypes[0];
		}

		$this->error = $this->composer->validateCreateDraftRequest($ct);

		if (!$this->error->has()) {
			// create the page
			$d = $this->composer->createDraft($ct);
			$controls = ComposerControl::getList($this->composer);
			$outputControls = array();
			foreach($controls as $cn) {
				$data = $cn->getRequestValue();
				$cn->publishToPage($d, $data, $controls);
				$outputControls[] = $cn;
			}
			$d->setPageNameFromComposerControls($outputControls);
			$configuredTarget = $this->composer->getComposerTargetObject();
			$targetPageID = $configuredTarget->getComposerConfiguredTargetParentPageID();
			if (!$targetPageID) {
				$targetPageID = $this->post('cParentID');
			}
			$d->setComposerDraftTargetParentPageID($targetPageID);
			$this->publish($d, $outputControls);
		}
	}

}