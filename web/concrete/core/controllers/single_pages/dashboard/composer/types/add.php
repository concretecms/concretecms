<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Types_Add extends DashboardBaseController {

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
		$ct = CollectionType::getByID($this->post('ctID'));
		if (!is_object($ct)) {
			$this->error->add(t('You must specify a valid page type for your composer.'));
		}
		$target = ComposerTargetType::getByID($this->post('cmpTargetTypeID'));
		if (!is_object($target)) {
			$this->error->add(t('Invalid composer target type.'));
		}

		if (!$this->error->has()) {
			$cmp = Composer::add($name, $ct);
			$configuredTarget = $target->configureComposerTarget($cmp, $this->post());
			$cmp->setConfiguredComposerTargetObject($configuredTarget);
			$this->redirect('/dashboard/composer/types', 'composer_added');
		}
	}

	public function on_start() {
		parent::on_start();
		$types = array();
		$pagetypes = CollectionType::getList();
		foreach($pagetypes as $ct) {
			$types[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
		}
		$this->set('types', $types);

		$this->set('targetTypes', ComposerTargetType::getList());
	}

}