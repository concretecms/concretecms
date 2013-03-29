<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_Write extends DashboardBaseController {

	public function view($cmpID = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!is_object($this->composer)) {
			$composers = Composer::getList();
			if (count($composers) == 1) {
				$cmp = $composers[0];
				$this->redirect('/dashboard/composer/write', $cmp->getComposerID());
			} else {
				$this->set('composers', $composers);
			}
		} else {
			$this->set('composer', $this->composer);
			$this->set('fieldsets', ComposerFormLayoutSet::getList($this->composer));
		}
	}

	public function save($cmpID = false) {
		session_write_close();
		$this->view($cmpID);
		$ct = CollectionType::getByID($this->post('cmpPageTypeID'));
		$availablePageTypes = $this->composer->getComposerPageTypeObjects();

		if (!is_object($ct)) {
			if (count($availablePageTypes) > 1) {
				$this->error->add(t('You must choose a page type.'));
			} else {
				$ct = $availablePageTypes[0];
			}
		} else if (!in_array($ct, $availablePageTypes)) {
			$this->error->add(t('This page type is not a valid page type for this composer.'));
		}

		$targetPage = $this->composer->getComposerSelectedTargetPageObject();
		if (!is_object($targetPage)) {
			$this->error->add(t('You must choose a page to publish this page beneath.'));
		}

		if (!$this->error->has()) {


		}

	}

}