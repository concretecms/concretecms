<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Single extends DashboardBaseController {

	public function single_page_added() {
		$this->set('message', t('Page Successfully Added.'));
		$this->view();
	}

	public function single_page_refreshed() {
		$this->set('message', t('Page Successfully Refreshed.'));
		$this->view();
	}

	public function view() {
		$this->set('generated', SinglePage::getList());
		if($this->isPost()) {
			if($this->token->validate('add_single_page')) {
				$pathToNode = SinglePage::getPathToNode($this->post('pageURL'), false);
				$path = SinglePage::sanitizePath($this->post('pageURL'));
				if (strlen($pathToNode) > 0) {
					// now we check to see if this is already added
					$pc = Page::getByPath('/' . $path, 'RECENT');
					if ($pc->getError() == COLLECTION_NOT_FOUND) {
						SinglePage::add($this->post('pageURL'));
						$this->redirect('/dashboard/pages/single', 'single_page_added');
					}
					else {
						$this->error->add(t("That page has already been added."));
					}
				}
				else {
					$this->error->add(t('That specified path doesn\'t appear to be a valid static page.'));
				}
			}
		}
	}

	public function refresh($cID = 0, $token) {
		if(intval($cID) > 0) {
			if($this->token->validate('refresh', $token)) {
				$p = SinglePage::getByID($cID);
				$cp = new Permissions($p);
				if($cp->canAdmin()) {
					$p->refresh();
					$this->redirect('/dashboard/pages/single', 'single_page_refreshed');
				}
				else {
					$this->error->add(t('You do not have permissions to refresh this page.'));
				}
			}
			else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
		else {
			$this->error->add(t('Page Unsuccessfully Refreshed.'));
		}
	}
}