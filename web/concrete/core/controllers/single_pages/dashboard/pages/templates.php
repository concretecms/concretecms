<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Templates extends DashboardBaseController {
	
	public function view() { 
		$this->set("icons", PageTemplate::getIcons());
		$this->set('templates', PageTemplate::getList());
	}	
	
	public function delete($pTemplateID, $token = '') {
		$db = Loader::db();
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_page_template', $token)) {
			$this->set('message', $valt->getErrorMessage());
		} else {
			$pt = PageTemplate::getByID($pTemplateID);
			$pt->delete();
			$this->redirect("/dashboard/pages/templates");
		}
	}
	
	public function page_template_added() {
		$this->set('message', t('Page template added successfully.'));
		$this->view();
	}

	public function page_template_updated() {
		$this->set('message', t('Page template updated successfully.'));
		$this->view();
	}
	
	public function update() {
		$valt = Loader::helper('validation/token');
		$pt = PageTemplate::getByID($_REQUEST['pTemplateID']);
		$pTemplateName = $_POST['ctName'];
		$pTemplateHandle = $_POST['ctHandle'];
		$vs = Loader::helper('validation/strings');
		
		if (!is_object($pt)) {
			$this->error->add(t('Invalid page template object.'));
		}

		if (!$pTemplateHandle) {
			$this->error->add(t("Handle required."));
		} else if (!$vs->handle($pTemplateHandle)) {
			$this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
		}
		
		if (!$pTemplateName) {
			$this->error->add(t("Name required."));
		} else if (preg_match('/[<>;{}?"`]/i', $pTemplateName)) {
			$this->error->add(t('Invalid characters in page template name.'));
		}
		
		
		if (!$valt->validate('update_page_template')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		if (!$this->error->has()) {
			$pt->update($pTemplateHandle, $pTemplateName, $pTemplateIcon);
			$this->redirect('/dashboard/pages/templates', 'page_template_updated');
		}	

		$this->view();

	
	}

}