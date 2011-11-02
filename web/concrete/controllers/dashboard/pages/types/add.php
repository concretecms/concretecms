<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('single_page');
Loader::model('collection_attributes');
class DashboardPagesTypesAddController extends Controller {
	
	
	public function view() { 
		$this->set("icons", CollectionType::getIcons());
	}	
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function update() {
	
	
	}

}