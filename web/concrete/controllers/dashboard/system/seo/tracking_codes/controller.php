<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemSeoTrackingCodesController extends DashboardBaseController {

	public function view() {
		$this->set('tracking_code', Config::get('SITE_TRACKING_CODE'));
		$this->set('tracking_code_position', Config::get('SITE_TRACKING_CODE_POSITION'));

		if ($this->isPost()) {
			if ($this->token->validate('update_tracking_code')) {
					Config::save('SITE_TRACKING_CODE', $this->post('tracking_code'));
					Config::save('SITE_TRACKING_CODE_POSITION', $this->post('tracking_code_position'));
			} else {
				$this->set('token_error', array($this->token->getErrorMessage()));
			}
		}
	}
}