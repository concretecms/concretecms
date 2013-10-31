<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Page_Dashboard_Composer_Write extends DashboardController {

	public function view($type = false, $id = false) {
		switch($type) {
			case 'composer':
				$this->pagetype = PageType::getByID($id);
				$saveURL = BASE_URL . View::url('/dashboard/composer/write', 'save', 'composer', $id);
				$discardURL = BASE_URL . View::url('/dashboard/composer/write', 'discard_exit');
				$viewURL = BASE_URL . View::url('/dashboard/composer/write', 'composer', $id);
				break;
			case 'draft':
				$this->draft = Page::getByID($id);
				if (is_object($this->draft)) {
					$this->checkDraftPermissions($this->draft);
					$this->pagetype = $this->draft->getPageTypeObject();
				}
				$saveURL = BASE_URL . View::url('/dashboard/composer/write', 'save', 'draft', $id);
				$discardURL = BASE_URL . View::url('/dashboard/composer/write', 'discard');
				$viewURL = BASE_URL . View::url('/dashboard/composer/write', 'draft', $id);
				break;
		}

		$this->requireAsset('core/composer');
		$cID = 0;
		if (is_object($this->draft)) {
			$cID = $this->draft->getCollectionID();
		}
		$token = Loader::helper('validation/token')->generate('composer');
		$js =<<<EOL
<script type="text/javascript">$(function() { $('form[data-form=composer]').ccmcomposer({token: '{$token}', autoSavePushViewState: true, autoSaveEnabled: false, cID: {$cID}, viewURL: '{$viewURL}', saveURL: '{$saveURL}', discardURL: '{$discardURL}'})});</script>
EOL;
		$this->addFooterItem($js);

		if (!is_object($this->pagetype)) {
			$pagetypes = PageType::getList();
			if (count($pagetypes) == 1) {
				$ptt = $pagetypes[0];
				$this->redirect('/dashboard/composer/write', 'composer', $ptt->getPageTypeID());
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

	protected function checkDraftPermissions(Page $d) {
		$dp = new Permissions($d);
		if (!$dp->canEditPageContents()) {
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

	protected function publish(Page $d, $outputControls) {

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
			$this->pagetype->publish($d);
			header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID());
		}
	}

	public function discard() {
		if (Loader::helper('validation/token')->validate('composer', $_POST['token'])) {
			$draft = Page::getByID($_POST['cID']);
			$this->checkDraftPermissions($draft);
			$draft->delete();
			$r = new PageTypePublishResponse();
			$r->setRedirectURL(View::url('/dashboard/composer/drafts'));
			print Loader::helper('ajax')->sendResult($r);
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			print Loader::helper("ajax")->sendError($this->error);
		}
	}

	public function discard_exit() {
		$r = new PageTypePublishResponse();
		$r->setRedirectURL(View::url('/dashboard/composer/drafts'));
		print Loader::helper('ajax')->sendResult($r);
		exit;
	}

	public function save($type = 'composer', $id = false, $action = 'return_json') {
		Cache::disableCache();
		Cache::disableLocalCache();
		session_write_close();

		$this->view($type, $id);
		$pt = PageTemplate::getByID($this->post('ptComposerPageTemplateID'));
		$availablePageTemplates = $this->pagetype->getPageTypePageTemplateObjects();

		if (!is_object($pt)) {
			$pt = $this->pagetype->getPageTypeDefaultPageTemplateObject();
		}

		$this->error = $this->pagetype->validateCreateDraftRequest($pt);

		if (!$this->error->has()) {
			// create the page
			if (!is_object($this->draft)) {
				$d = $this->pagetype->createDraft($pt);
			} else {
				// if we have a draft, but the page type has changed, we create a new one.
				$d = $this->draft;
				$d = $d->cloneVersion('');
			}

			/// set the target
			$configuredTarget = $this->pagetype->getPageTypePublishTargetObject();
			$targetPageID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
			if (!$targetPageID) {
				$targetPageID = $this->post('cParentID');
			}
			$d->setPageDraftTargetParentPageID($targetPageID);
			$outputControls = $this->pagetype->savePageTypeComposerForm($d);

			$r = new PageTypePublishResponse();
			$r->setPage($d);
			$r->setViewURL(BASE_URL . View::url('/dashboard/composer/write', 'draft', $d->getCollectionID()));
			$r->setSaveURL(BASE_URL . View::url('/dashboard/composer/write', 'save', 'draft', $d->getCollectionID()));
			$r->setDiscardURL(BASE_URL . View::url('/dashboard/composer/write', 'discard'));

			$ax = Loader::helper('ajax');

			if ($_POST['task'] == 'autosave') {
				$r->setMessage(t('Page saved on %s', $r->time));
				$r->outputJSON();
			}
			if ($_POST['task'] == 'save') {
				$r->setRedirectURL(View::url('/dashboard/composer/drafts'));
				$ax->sendResult($r);
			}
			if ($_POST['task'] == 'preview') {
				$r->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out&' . Loader::helper('validation/token')->getParameter());
				$ax->sendResult($r);
			}

			if ($_POST['task'] == 'publish') {

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
					$r->setPage($d);
					$this->pagetype->publish($d);
					$r->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID());
					$ax->sendResult($r);
				}
			}
		}

		if ($this->error->has()) {
			print Loader::helper("ajax")->sendError($this->error);
		}
	}

}