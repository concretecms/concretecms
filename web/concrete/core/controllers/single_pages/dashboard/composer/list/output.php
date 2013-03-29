<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Output extends DashboardBaseController {

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


}