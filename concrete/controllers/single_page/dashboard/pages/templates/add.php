<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Templates;
use \Concrete\Core\Page\Controller\DashboardPageController;
use PageTemplate;
use Loader;

class Add extends DashboardPageController {
	
	public function on_start() { 
		parent::on_start();
		$this->set("icons", PageTemplate::getIcons());
	}	
		
	public function add_page_template() {
		$pTemplateName = $_POST['pTemplateName'];
		$pTemplateHandle = $_POST['pTemplateHandle'];
		$pTemplateIcon = $_POST['pTemplateIcon'];
		$vs = Loader::helper('validation/strings');
		
		$error = array();
		if (!$pTemplateHandle) {
			$this->error->add(t("Handle required."));
		} else if (!$vs->handle($pTemplateHandle)) {
			$this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
		}
		
		if (!$pTemplateName) {
			$this->error->add(t("Name required."));
		} else if (preg_match('/[<>{};?"`]/i', $pTemplateName)) {
			$this->error->add(t('Invalid characters in page template name.'));
		}

		$valt = Loader::helper('validation/token');
		if (!$valt->validate('add_page_template')) {
			$this->error->add($valt->getErrorMessage());
		}
	
		if (!$this->error->has()) { 
			$pt = PageTemplate::add($pTemplateHandle, $pTemplateName, $pTemplateIcon);
			$this->redirect('/dashboard/pages/templates', 'page_template_added');
		}
	}

}