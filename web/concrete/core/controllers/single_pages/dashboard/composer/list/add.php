<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Add extends DashboardBaseController {

	public function submit() {
		$vs = Loader::helper('validation/strings');
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('cmpName'));
		if (!$this->token->validate('add_composer')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}
		if (!$vs->notempty($name)) {
			$this->error->add(t('You must specify a valid name for your composer.'));
		}
		$defaultTemplate = PageTemplate::getByID($this->post('cmpDefaultPageTemplateID'));
		if (!is_object($defaultTemplate)) {
			$this->error->add(t('You must choose a valid default page template.'));
		}
		$templates = array();
		if (is_array($_POST['cmpPageTemplateID'])) {
			foreach($this->post('cmpPageTemplateID') as $pageTemplateID) {
				$pt = PageTemplate::getByID($pageTemplateID);
				if (is_object($pt)) {
					$templates[] = $pt;
				}
			}
		}
		if (count($templates) == 0 && $this->post('cmpAllowedPageTemplates') == 'C') {
			$this->error->add(t('You must specify at least one page template.'));
		}
		$target = ComposerTargetType::getByID($this->post('cmpTargetTypeID'));
		if (!is_object($target)) {
			$this->error->add(t('Invalid composer target type.'));
		}

		if (!$this->error->has()) {
			$cmp = Composer::add($name, $defaultTemplate, $this->post('cmpAllowedPageTemplates'), $templates);
			$configuredTarget = $target->configureComposerTarget($cmp, $this->post());
			$cmp->setConfiguredComposerTargetObject($configuredTarget);
			$this->redirect('/dashboard/composer/list', 'composer_added');
		}
	}

}