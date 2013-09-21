<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($type = false, $id = false) {
		switch($type) {
			case 'composer':
				$this->pagetype = PageType::getByID($id);
				$saveURL = View::url('/dashboard/composer/write', 'save', 'composer', $id);
				$discardURL = '';
				$publishURL = View::url('/dashboard/composer/write', 'save', 'composer', $id, 'publish');
				$viewURL = View::url('/dashboard/composer/write', 'composer', $id);
				break;
			case 'draft':
				$this->draft = PageDraft::getByID($id);
				if (is_object($this->draft)) {
					$this->checkDraftPermissions($this->draft);
					$this->pagetype = $this->draft->getPageTypeObject();
				}
				$saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
				$discardURL = View::url('/dashboard/composer/write', 'discard', $id, Loader::helper('validation/token')->generate('discard_draft'));
				$publishURL = View::url('/dashboard/composer/write', 'save', 'draft', $id, 'publish');
				$viewURL = View::url('/dashboard/composer/write', 'draft', $id);
				break;
		}

		$this->requireAsset('core/composer');
		$pDraftID = 0;
		if (is_object($this->draft)) {
			$pDraftID = $this->draft->getPageDraftID();
		}
		$js =<<<EOL
<script type="text/javascript">$(function() { $('form[data-form=composer]').ccmcomposer({pushStateOnSave: true, pDraftID: {$pDraftID}, viewURL: '{$viewURL}', saveURL: '{$saveURL}', discardURL: '{$discardURL}', publishURL: '{$publishURL}'})});</script>
EOL;
		$this->addFooterItem($js);

		if (!is_object($this->pagetype)) {
			$pagetypes = PageType::getList();
			if (count($pagetypes) == 1) {
				$ptt = $pagetypes[0];
				$this->redirect('/dashboard/composer/write', 'pagetype', $ptt->getPageTypeID());
			} else {
				$this->set('pagetypes', $pagetypes);
			}
		} else {
			$ccp = new Permissions($this->pagetype);
			if (!$ccp->canComposePageType()) {
				throw new Exception('You do not have access to this page type.');
			}
			$this->set('pagetype', $this->pagetype);
			$this->set('fieldsets', PageTypeComposerFormLayoutSet::getList($this->pagetype));
			$this->set('draft', $this->draft);
			$this->setupAssets();
		}
	}

	protected function checkDraftPermissions(PageDraft $d) {
		$dp = new Permissions($d);
		if (!$dp->canEditPageDraft()) {
			throw new Exception('You do not have access to this draft.');
		}
	}

	protected function setupAssets() {
		$sets = $this->get('fieldsets');
		foreach($sets as $s) {
			$controls = PageTypeComposerFormLayoutSetControl::getList($s);
			foreach($controls as $cn) {
				$basecontrol = $cn->getPageTypeComposerControlObject();
			}
		}
	}

	protected function publish(PageDraft $d, $outputControls) {

		if (!$d->getPageDraftTargetParentPageID()) {
			$this->error->add(t('You must choose a page to publish this page beneath.'));
		}

		foreach($outputControls as $oc) {
			if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
				$data = $oc->getRequestValue();
				$oc->validate($data, $this->error);
			}
		}

		if (!$this->error->has()) {
			$d->publish();
			$c = $d->getPageDraftCollectionObject();
			header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		}
	}

	public function discard($pDraftID = false, $token = false) {
		if (Loader::helper('validation/token')->validate('discard_draft', $token)) {
			$draft = PageDraft::getByID($pDraftID);
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
		$pt = PageTemplate::getByID($this->post('ptComposerPageTemplateID'));
		$availablePageTemplates = $this->pagetype->getPageTypePageTemplateObjects();

		if (!is_object($pt) && count($availablePageTemplates) == 1) {
			$pt = $availablePageTemplates[0];
		}

		$this->error = $this->pagetype->validateCreateDraftRequest($pt);

		if (!$this->error->has()) {
			// create the page
			if (!is_object($this->draft)) {
				$d = $this->pagetype->createDraft($pt);
			} else {
				// if we have a draft, but the page type has changed, we create a new one.
				$d = $this->draft;
				$dc = $d->getPageDraftCollectionObject();
				if ($dc->getPageTemplateID() != $_POST['ptComposerPageTemplateID']) {
					$d = $this->pagetype->createDraft($pt);
				} else {
					$d->createNewCollectionVersion();
				}
			}

			/// set the target
			$configuredTarget = $this->pagetype->getPageTypePublishTargetObject();
			$targetPageID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
			if (!$targetPageID) {
				$targetPageID = $this->post('cParentID');
			}
			$d->setPageDraftTargetParentPageID($targetPageID);
			$outputControls = $d->saveForm();

			$r = new PageTypePublishResponse();
			$r->setPageDraft($d);

			if ($action == 'return_json') {
				$ax = Loader::helper('ajax');
				$r->time = date('F d, Y \a\t g:i A');
				$r->setDraftSaveStatus(t('Page saved on %s', $r->time));
				$r->setSaveURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getPageDraftID()));
				$r->setViewURL(View::url('/dashboard/composer/write', 'draft', $d->getPageDraftID()));
				$r->setDiscardURL(View::url('/dashboard/composer/write', 'discard', $d->getPageDraftID(), Loader::helper('validation/token')->generate('discard_draft')));
				$r->setPublishURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getPageDraftID(), 'publish'));
				$ax->sendResult($r);
			} else if ($action == 'publish') {
				$this->publish($d, $outputControls);
			}
		}
	}

}