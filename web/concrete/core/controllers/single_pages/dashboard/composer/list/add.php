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
		$types = array();
		if (is_array($_POST['cmpCTID'])) {
			foreach($this->post('cmpCTID') as $ctID) {
				$ct = CollectionType::getByID($ctID);
				if (is_object($ct)) {
					$types[] = $ct;
				}
			}
		}
		if (count($types) == 0) {
			$this->error->add(t('You must specify at least one page type.'));
		}
		$target = ComposerTargetType::getByID($this->post('cmpTargetTypeID'));
		if (!is_object($target)) {
			$this->error->add(t('Invalid composer target type.'));
		}

		if (!$this->error->has()) {
			$cmp = Composer::add($name, $types);
			$configuredTarget = $target->configureComposerTarget($cmp, $this->post());
			$cmp->setConfiguredComposerTargetObject($configuredTarget);
			$this->redirect('/dashboard/composer/list', 'composer_added');
		}
	}

}