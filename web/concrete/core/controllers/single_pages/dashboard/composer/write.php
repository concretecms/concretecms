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
				$this->draft = Page::getByID($id);
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
		$cID = 0;
		if (is_object($this->draft)) {
			$cID = $this->draft->getCollectionID();
		}
		$js =<<<EOL
<script type="text/javascript">$(function() { $('form[data-form=composer]').ccmcomposer({pushStateOnSave: true, cID: {$cID}, viewURL: '{$viewURL}', saveURL: '{$saveURL}', discardURL: '{$discardURL}', publishURL: '{$publishURL}'})});</script>
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
		if (!$dp->canEditPage()) {
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

		if (!$d->getPageTargetParentPageID()) {
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
			header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID());
		}
	}

	public function discard($cID = false, $token = false) {
		if (Loader::helper('validation/token')->validate('discard_draft', $token)) {
			$draft = Page::getByID($cID);
			$this->checkDraftPermissions($draft);
			$draft->delete();
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
				if ($d->getPageTemplateID() != $_POST['ptComposerPageTemplateID']) {
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
			$outputControls = $this->pagetype->savePageTypeComposerForm($d);

			$r = new PageTypePublishResponse();
			$r->setPage($d);

			if ($action == 'return_json') {
				$ax = Loader::helper('ajax');
				$r->time = date('F d, Y \a\t g:i A');
				$r->setDraftSaveStatus(t('Page saved on %s', $r->time));
				$r->setSaveURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getCollectionID()));
				$r->setViewURL(View::url('/dashboard/composer/write', 'draft', $d->getCollectionID()));
				$r->setDiscardURL(View::url('/dashboard/composer/write', 'discard', $d->getCollectionID(), Loader::helper('validation/token')->generate('discard_draft')));
				$r->setPublishURL(View::url('/dashboard/composer/write', 'save', 'draft', $d->getCollectionID(), 'publish'));
				$ax->sendResult($r);
			} else if ($action == 'publish') {
				$this->publish($d, $outputControls);
			} else if ($action == 'redirect') {
				header('Location:' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
				exit;
			}
		}
	}

}