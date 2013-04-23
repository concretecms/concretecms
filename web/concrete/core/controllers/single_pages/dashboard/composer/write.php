<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($type = false, $id = false) {
		switch($type) {
			case 'composer':
				$this->composer = Composer::getByID($id);
				$saveURL = View::url('/dashboard/composer/write', 'save', 'composer', $id);
				$discardURL = '';
				$publishURL = View::url('/dashboard/composer/write', 'save', 'composer', $id, 'publish');
				break;
			case 'draft':
				$this->draft = ComposerDraft::getByID($id);
				if (is_object($this->draft)) {
					$this->checkDraftPermissions($this->draft);
					$this->composer = $this->draft->getComposerObject();
				}
				$saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
				$discardURL = View::url('/dashboard/composer/write', 'discard', $id, Loader::helper('validation/token')->generate('discard_draft'));
				$publishURL = View::url('/dashboard/composer/write', 'save', 'draft', $id, 'publish');
				break;
		}

		$this->addHeaderItem(Loader::helper('html')->css('ccm.composer.css'));
		$this->addFooterItem(Loader::helper('html')->javascript('ccm.composer.js'));
		$cmpDraftID = 0;
		if (is_object($this->draft)) {
			$cmpDraftID = $this->draft->getComposerDraftID();
		}
		$js =<<<EOL
<script type="text/javascript">$(function() { $('form[data-form=composer]').ccmcomposer({cmpDraftID: {$cmpDraftID}, saveURL: '{$saveURL}', discardURL: '{$discardURL}', publishURL: '{$publishURL}'})});</script>
EOL;
		$this->addFooterItem($js);

		if (!is_object($this->composer)) {
			$composers = Composer::getList();
			if (count($composers) == 1) {
				$cmp = $composers[0];
				$this->redirect('/dashboard/composer/write', 'composer', $cmp->getComposerID());
			} else {
				$this->set('composers', $composers);
			}
		} else {
			$ccp = new Permissions($this->composer);
			if (!$ccp->canAccessComposer()) {
				throw new Exception('You do not have access to this composer.');
			}
			$this->set('composer', $this->composer);
			$this->set('fieldsets', ComposerFormLayoutSet::getList($this->composer));
			$this->set('draft', $this->draft);
			$this->setupAssets();
		}
	}

	protected function checkDraftPermissions(ComposerDraft $d) {
		$dp = new Permissions($d);
		if (!$dp->canEditComposerDraft()) {
			throw new Exception('You do not have access to this draft.');
		}
	}

	protected function setupAssets() {
		$sets = $this->get('fieldsets');
		foreach($sets as $s) {
			$controls = ComposerFormLayoutSetControl::getList($s);
			foreach($controls as $cn) {
				$basecontrol = $cn->getComposerControlObject();
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

	public function discard($cmpDraftID = false, $token = false) {
		if (Loader::helper('validation/token')->validate('discard_draft', $token)) {
			$draft = ComposerDraft::getByID($cmpDraftID);
			$this->checkDraftPermissions($draft);
			$draft->discard();
			exit;
		}
	}

	public function save($type = 'composer', $id = false, $action = 'return_json') {
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
				$d->createNewCollectionVersion();
			}

			/// set the target
			$configuredTarget = $this->composer->getComposerTargetObject();
			$targetPageID = $configuredTarget->getComposerConfiguredTargetParentPageID();
			if (!$targetPageID) {
				$targetPageID = $this->post('cParentID');
			}
			$d->setComposerDraftTargetParentPageID($targetPageID);
			$outputControls = $d->saveForm();

			$r = new ComposerPublishResponse();
			$r->setComposerDraft($d);

			if ($action == 'return_json') {
				$ax = Loader::helper('ajax');
				$r->time = date('F d, Y g:i A');
				$r->setSaveURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getComposerDraftID()));
				$r->setDiscardURL(View::url('/dashboard/composer/write', 'discard', $d->getComposerDraftID(), Loader::helper('validation/token')->generate('discard_draft')));
				$r->setPublishURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getComposerDraftID(), 'publish'));
				$ax->sendResult($r);
			} else if ($action == 'publish') {
				$this->publish($d, $outputControls);
			}
		}
	}

}