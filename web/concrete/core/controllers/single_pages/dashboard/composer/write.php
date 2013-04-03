<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($type = false, $id = false) {
		switch($type) {
			case 'composer':
				$this->composer = Composer::getByID($id);
				$this->set('saveURL', View::url('/dashboard/composer/write', 'save', 'composer', $id));
				break;
			case 'draft':
				$this->draft = ComposerDraft::getByID($id);
				if (is_object($this->draft)) {
					$this->composer = $this->draft->getComposerObject();
				}
				$this->set('saveURL', View::url('/dashboard/composer/write', 'save', 'draft', $id));
				break;
		}

		if (!is_object($this->composer)) {
			$composers = Composer::getList();
			if (count($composers) == 1) {
				$cmp = $composers[0];
				$this->redirect('/dashboard/composer/write', 'composer', $cmp->getComposerID());
			} else {
				$this->set('composers', $composers);
			}
		} else {
			$this->set('composer', $this->composer);
			$this->set('fieldsets', ComposerFormLayoutSet::getList($this->composer));
			$this->set('draft', $this->draft);
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

	public function save($type = 'composer', $id = false, $return = 'json') {
		Cache::disableCache();
		Cache::disableLocalCache();
		session_write_close();

		$this->view($type, $id);
		$ct = CollectionType::getByID($this->post('cmpPageTypeID'));
		$availablePageTypes = $this->composer->getComposerPageTypeObjects();

		if (!is_object($ct) && count($availablePageTypes) == 1) {
			$ct = $availablePageTypes[0];
		}

		$this->error = $this->composer->validateCreateDraftRequest($ct);

		if (!$this->error->has()) {
			// create the page
			if (!is_object($this->draft)) {
				$d = $this->composer->createDraft($ct);
			} else {
				$d = $this->draft;
			}

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
			if ($return == 'json') {
				$ax = Loader::helper('ajax');
				$r = new stdClass;
				$r->time = date('F d, Y g:i A');
				$r->cmpDraftID = $d->getComposerDraftID();
				$ax->sendResult($r);
			}
			//$this->publish($d, $outputControls);
		}
	}

}