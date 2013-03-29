<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($cmpID = false) {
		$cmp = Composer::getByID($cmpID);
		if (!is_object($cmp)) {
			$composers = Composer::getList();
			if (count($composers) == 1) {
				$cmp = $composers[0];
				$this->redirect('/dashboard/composer/write', $cmp->getComposerID());
			} else {
				$this->set('composers', $composers);
			}
		} else {
			$this->set('composer', $cmp);
			$this->set('fieldsets', ComposerFormLayoutSet::getList($cmp));
		}
	}

}