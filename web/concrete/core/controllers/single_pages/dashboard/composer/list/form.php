<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Form extends DashboardBaseController {

	public function view($cmpID = false, $message = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		switch($message) {
			case 'layout_set_added':
				$this->set('success', t('Form layout set added.'));
				break;
			case 'layout_set_deleted':
				$this->set('success', t('Form layout set deleted.'));
				break;
			case 'layout_set_updated':
				$this->set('success', t('Form layout set updated.'));
				break;
		}
		$this->set('composer', $this->composer);
		$this->set('sets', ComposerFormLayoutSet::getList($this->composer));
	}

	public function delete_set($cmpFormLayoutSetID = false) {
		$set = ComposerFormLayoutSet::getByID($cmpFormLayoutSetID);
		if (!is_object($set)) {
			$this->redirect('/dashboard/composer/list');
		}
		$this->view($set->getComposerID());
		if (!$this->token->validate('delete_set')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		if (!$this->error->has()) {
			$set->delete();
			$this->redirect('/dashboard/composer/list/form', $set->getComposerID(), 'layout_set_deleted');
		}
	}

	public function update_set($cmpFormLayoutSetID = false) {
		$set = ComposerFormLayoutSet::getByID($cmpFormLayoutSetID);
		if (!is_object($set)) {
			$this->redirect('/dashboard/composer/list');
		}
		$this->view($set->getComposerID());
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('cmpFormLayoutSetName'));
		if (!$this->token->validate('update_set')) { 
			$this->error->add(t($this->token->getErrorMessage()));
		}

		if (!$this->error->has()) {
			$set->updateFormLayoutSetName($name);
			$this->redirect('/dashboard/composer/list/form', $set->getComposerID(), 'layout_set_updated');
		}
	}

	public function delete_set_control() {
		$control = ComposerFormLayoutSetControl::getByID($this->post('cmpFormLayoutSetControlID'));
		if (is_object($control)) {
			if ($this->token->validate('delete_set_control', $_POST['token'])) {
				$control->delete();
			}
		}
		exit;
	}

	public function add_set($cmpID = false) {
		$this->view($cmpID);
		$sec = Loader::helper('security');
		$name = $sec->sanitizeString($this->post('cmpFormLayoutSetName'));
		if ($this->token->validate('add_set')) {
			$set = $this->composer->addComposerFormLayoutSet($name);
			$this->redirect('/dashboard/composer/list/form', $this->composer->getComposerID(), 'layout_set_added');
		}
	}

	public function update_set_control_display_order() {
		$fs = ComposerFormLayoutSet::getByID($_POST['cmpFormLayoutSetID']);
		if (is_object($fs)) {
			if ($this->token->validate('update_set_control_display_order', $_POST['token'])) {
				$displayOrder = 0;
				foreach($this->post('cmpFormLayoutSetControlID') as $cmpFormLayoutSetControlID) {
					$control = ComposerFormLayoutSetControl::getByID($cmpFormLayoutSetControlID);
					if (is_object($control)) {
						$control->updateFormLayoutSetControlDisplayOrder($displayOrder);
						$displayOrder++;
					}
				}
			}
		}
		exit;
	}

	public function update_set_display_order() {
		$this->view($this->post('cmpID'));
		if ($this->token->validate('update_set_display_order', $_POST['token'])) {
			$displayOrder = 0;
			foreach($this->post('cmpFormLayoutSetID') as $cmpFormLayoutSetID) {
				$set = ComposerFormLayoutSet::getByID($cmpFormLayoutSetID);
				if (is_object($set)) {
					$set->updateFormLayoutSetDisplayOrder($displayOrder);
					$displayOrder++;
				}
			}
		}
		exit;
	}

}