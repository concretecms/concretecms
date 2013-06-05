<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List extends DashboardBaseController {

	public function composer_added() {
		$this->set('success', t('Composer added successfully.'));
		$this->view();
	}

	public function composer_updated() {
		$this->set('success', t('Composer updated successfully.'));
		$this->view();
	}

	public function composer_deleted() {
		$this->set('success', t('Composer deleted successfully.'));
		$this->view();
	}

	public function edit($cmpID = false) {
		$cm = Composer::getByID($cmpID);
		$this->set('composer', $cm);
	}

	public function view() {
		$composers = Composer::getList();
		$this->set('composers', $composers);
	}

	public function delete($cmpID = false) {
		$composer = Composer::getByID($cmpID);
		if (!is_object($composer)) {
			$this->error->add(t('Invalid composer object.'));
		}
		if (!$this->token->validate('delete_composer')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}
		if (!$this->error->has()) {
			$composer->delete();
			$this->redirect('/dashboard/composer/list', 'composer_deleted');
		}
	}
	
	public function submit($cmpID = false) {
		$composer = Composer::getByID($cmpID);
		if (!is_object($composer)) {
			$this->error->add(t('Invalid composer object.'));
		}
		if (!$this->token->validate('update_composer')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		$vs = Loader::helper('validation/strings');
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('cmpName'));
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
		if (count($types) == 0 && $this->post('cmpAllowedPageTypes') == 'C') {
			$this->error->add(t('You must specify at least one page type.'));
		}
		$target = ComposerTargetType::getByID($this->post('cmpTargetTypeID'));
		if (!is_object($target)) {
			$this->error->add(t('Invalid composer target type.'));
		}
		if (!$this->error->has()) {
			$composer->update($name, $this->post('cmpAllowedPageTypes'), $types);
			$configuredTarget = $target->configureComposerTarget($composer, $this->post());
			$composer->setConfiguredComposerTargetObject($configuredTarget);
			$this->redirect('/dashboard/composer/list', 'composer_updated');
		}
	}
}