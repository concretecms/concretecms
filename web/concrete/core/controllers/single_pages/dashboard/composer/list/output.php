<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Output extends DashboardBaseController {

	public function view($cmpID = false, $message = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		// redirects into the order so we keep areas up to date
		foreach($this->composer->getComposerPageTemplateObjects() as $pt) {
			$pt->refreshPageTemplateAreas();
		}
		exit;
		$this->redirect('/dashboard/composer/list/output', 'order', $cmpID);
	}

	public function update_output_control_display_order() {
		$cmp = Composer::getByID($_POST['cmpID']);
		if (is_object($cmp)) {
			if ($this->token->validate('update_output_control_display_order', $_POST['token'])) {
				foreach($this->post('area') as $arHandle => $controls) {
					$displayOrder = 0;
					foreach($controls as $controlID) {
						$control = ComposerOutputControl::getByID($controlID);
						$control->updateComposerOutputControlArea($arHandle);
						$control->updateComposerOutputControlDisplayOrder($displayOrder);
						$displayOrder++;
					}
				}
			}
		}

		exit;
	}

	public function order($cmpID = false, $message = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		$this->set('composer', $this->composer);
	}




}