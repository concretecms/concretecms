<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Composer_List_Output extends DashboardBaseController {

	public function view($cmpID = false, $message = false) {
		$this->composer = Composer::getByID($cmpID);
		if (!$this->composer) {
			$this->redirect('/dashboard/composer/list');
		}
		// redirects into the order so we keep areas up to date
		foreach($this->composer->getComposerPageTypeObjects() as $ct) {
			$mc = $ct->getMasterTemplate();
			// there's gotta be a better way to do this.
			$_v = View::getInstance();
			$_v->disableEditing();
			$_v->setCollectionObject($mc);
			$_v->disableLinks();
			ob_start();
			$_v->render($mc);
			$ret = ob_get_contents();
			ob_end_clean();
			print $ret;
			unset($_v);
		}
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
		$this->set('areas', $this->composer->getPageTypeAreaList());
	}




}