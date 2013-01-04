<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_Excluded extends DashboardBaseController {

	public function view() {
		if ($this->post('SEO_EXCLUDE_WORDS')) {
			Config::save('SEO_EXCLUDE_WORDS',$this->post('SEO_EXCLUDE_WORDS'));
			$this->set('message','Excluded words saved.');
		}
		$this->set('SEO_EXCLUDE_WORDS',Config::get('SEO_EXCLUDE_WORDS'));
	}

}