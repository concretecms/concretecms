<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_Excluded extends DashboardBaseController {
		
	public function save() {
		Config::save('SEO_EXCLUDE_WORDS',$this->post('SEO_EXCLUDE_WORDS'));
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
		$this->set('SEO_EXCLUDE_WORDS_ORIGINAL_ARRAY', Urlify::get_original_removed_list());
		$this->set('SEO_EXCLUDE_WORDS_ARRAY', Urlify::get_removed_list());
		$this->set('SEO_EXCLUDE_WORDS',SEO_EXCLUDE_WORDS);
	}

}