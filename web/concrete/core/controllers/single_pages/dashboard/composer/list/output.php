<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Output extends DashboardBaseController {

	public function view($cmpID = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		$this->set('composer', $this->composer);
	}


	public function edit_defaults($cmpID = false, $pTemplateID = false) {
		$this->view($cmpID);
		$template = PageTemplate::getByID($pTemplateID);
		if (!is_object($template)) {
			$this->redirect('/dashboard/composer/list');
		}
		$valid = false;
		foreach($this->composer->getComposerPageTemplateObjects() as $pt) {
			if ($pt->getPageTemplateID() == $template->getPageTemplateID()) {
				$valid = true;
				break;
			}
		}
		if (!$valid) {
			$this->error->add(t('Invalid page template.'));
		}
		if (!$this->error->has()) {
			// we load up the master template for this composer/template combination.
			$c = $this->composer->getComposerPageTemplateDefaultPageObject($template);
			$_SESSION['mcEditID'] = $c->getCollectionID();
			header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
		}

	}




}