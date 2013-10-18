<?
defined('C5_EXECUTE') or die("Access Denied.");
Cache::disableCache();
Cache::disableLocalCache();
session_write_close();
$e = Loader::helper('validation/error');
if (!Loader::helper('validation/token')->validate('composer', $_REQUEST['token'])) {
	$e->add(Loader::helper('validation/token')->getErrorMessage());
}

$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if (!$cp->canEditPage()) {
	$e->add(t('Access Denied.'));
}

if (!$e->has()) {

	$pagetype = $c->getPageTypeObject();

	$pt = PageTemplate::getByID($_POST['ptComposerPageTemplateID']);
	$availablePageTemplates = $pagetype->getPageTypePageTemplateObjects();

	if (!is_object($pt)) {
		$pt = $pagetype->getPageTypeDefaultPageTemplateObject();
	}

	$e = $pagetype->validateCreateDraftRequest($pt);

	if (!$e->has()) {
		$c = $c->cloneVersion('');

		/// set the target
		$configuredTarget = $pagetype->getPageTypePublishTargetObject();
		$targetPageID = $configuredTarget->getPageTypePublishTargetConfiguredTargetParentPageID();
		if (!$targetPageID) {
			$targetPageID = $_POST['cParentID'];
		}
		$c->setPageDraftTargetParentPageID($targetPageID);
		$outputControls = $pagetype->savePageTypeComposerForm($c);


		// if we are exiting after this save, we need to have somewhere to redirect to.
		if ($_POST['task'] == 'save' || $_POST['task'] == 'autosave') {
			$ptr = new PageTypePublishResponse();
			$ptr->setPage($c);
			$ptr->setMessage(t('Page saved on %s', $ptr->time));
		}
		if ($_POST['task'] == 'save') {
			$u = new User();
			$cID = $u->getPreviousFrontendPageID();
			$ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
		}
		if ($_POST['task'] == 'preview') {
			$ptr = new PageTypePublishResponse();
			$ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=check-out&' . Loader::helper('validation/token')->getParameter());
		}

		if ($_POST['task'] == 'publish') {

			if (!$c->getPageDraftTargetParentPageID()) {
				$e->add(t('You must choose a page to publish this page beneath.'));
			}

			foreach($outputControls as $oc) {
				if ($oc->isPageTypeComposerFormControlRequiredOnThisRequest()) {
					$data = $oc->getRequestValue();
					$oc->validate($data, $e);
				}
			}

			if (!$e->has()) {
				$ptr = new PageTypePublishResponse();
				$ptr->setPage($c);
				$pagetype->publish($c);
				$ptr->setRedirectURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
			}
		}

	}
}

if (!($ptr instanceof PageTypePublishResponse)) {
	$ptr = new PageTypePublishResponse($e);
}

$ptr->outputJSON();