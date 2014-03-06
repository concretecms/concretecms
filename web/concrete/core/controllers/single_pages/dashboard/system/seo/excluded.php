<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_Excluded extends DashboardBaseController {
		
	public function save() {
		Config::save('SEO_EXCLUDE_WORDS',Loader::helper('security')->sanitizeString($this->post('SEO_EXCLUDE_WORDS')));
		$this->redirect('/dashboard/system/seo/excluded', 'saved');
	}

	public function reset() {
		Config::clear("SEO_EXCLUDE_WORDS");
		$this->redirect('/dashboard/system/seo/excluded', 'reset_complete');
	}
	
	public function view($message = false) {
		if ($message) {
			switch($message) {
				case 'reset_complete':
					$this->set('message', t('Reserved words reset.'));
					break;
				case 'saved':
					$this->set('success', t('Reserved words updated.'));
					break;
			}
		}
		Loader::library('3rdparty/urlify');
		$this->set('SEO_EXCLUDE_WORDS_ORIGINAL_ARRAY', Urlify::$remove_list);
		$excludeSeoWords = Config::get('SEO_EXCLUDE_WORDS');
		if(is_string($excludeSeoWords)) {
			if(strlen($excludeSeoWords)) {
				$remove_list = explode(',', $excludeSeoWords);
				$remove_list = array_map('trim', $remove_list);
				$remove_list = array_filter($remove_list, 'strlen');
			}
			else {
				$remove_list = array();
			}
		}
		else {
			$remove_list = Urlify::$remove_list;
		}
		$this->set('SEO_EXCLUDE_WORDS_ARRAY', $remove_list);
		$this->set('SEO_EXCLUDE_WORDS',SEO_EXCLUDE_WORDS);
	}

}
