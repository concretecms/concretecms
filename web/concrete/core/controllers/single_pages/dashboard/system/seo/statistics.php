<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_Statistics extends DashboardBaseController{

	public function view($updated = false) {
		if ($this->isPost()) {
			$sv = $this->post('STATISTICS_TRACK_PAGE_VIEWS') == 1 ? 1 : 0;
			Config::save('STATISTICS_TRACK_PAGE_VIEWS', $sv);
			$this->redirect('/dashboard/system/seo/statistics','1');
		}
		if($updated) {
			$this->set('message', t('Statistics tracking preference saved.'));
		}
		$this->set('STATISTICS_TRACK_PAGE_VIEWS', Config::get('STATISTICS_TRACK_PAGE_VIEWS'));
	}

}