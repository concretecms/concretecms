<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_Excluded extends DashboardBaseController {
		
	public function save() {
		Config::save('SEO_EXCLUDE_WORDS',$this->post('SEO_EXCLUDE_WORDS'));
		$this->redirect($this->getCollectionObject()->getCollectionPath(),'1');
	}
	
	public function view($saved = false) {
		if($saved) {
			$this->set('message','Excluded words saved.');
		}
		$this->set('SEO_EXCLUDE_WORDS',SEO_EXCLUDE_WORDS);
	}

}