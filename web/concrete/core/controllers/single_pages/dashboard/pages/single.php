<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Single extends DashboardBaseController {

	public function view($message = null, $error = null) {
		Loader::model('single_page');
		$this->set('generated', SinglePage::getList());
		if($message && !$error) {
			$this->set('message', $message);
		} else if($message) {
			$this->error->add($message);
		}
		if($this->isPost()) {
			if($this->token->validate('add_single_page')) {
				$pathToNode = SinglePage::getPathToNode($this->post('pageURL'), false);
				$path = SinglePage::sanitizePath($this->post('pageURL'));
		
				if (strlen($pathToNode) > 0) {
					// now we check to see if this is already added
					$pc = Page::getByPath('/' . $path, 'RECENT');
					
					if ($pc->getError() == COLLECTION_NOT_FOUND) {
						SinglePage::add($this->post('pageURL'));
						$this->redirect('/dashboard/pages/single', t('Page Successfully Added.'));
					} else {
						$this->redirect('/dashboard/pages/single', t("That page has already been added."), 1);
					}
				} else {
					$this->redirect('/dashboard/pages/single', t('That specified path doesn\'t appear to be a valid static page.'), 1);
				}
			}
			$this->redirect('/dashboard/pages/single', $this->token->getErrorMessage(), 1);
		}
	}
	
	public function refresh($cID = 0, $token) {
		if(intval($cID) > 0) {
			if($this->token->validate('refresh', $token)) {
				Loader::model('single_page');
				$p = SinglePage::getByID($cID);
				$cp = new Permissions($p);
				if($cp->canAdmin()) {
					$p->refresh();
					$this->redirect('/dashboard/pages/single', t('Page Successfully Refreshed.'));
				}
				$this->redirect('/dashboard/pages/single', t('You do not have permissions to refresh this page.'), 1);
			}
			$this->redirect('/dashboard/pages/single', $this->token->getErrorMessage(), 1);
		}
		$this->redirect('/dashboard/pages/single', t('Page Unsuccessfully Refreshed.'), 1);
	}
}