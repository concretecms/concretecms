<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Seo_SearchIndex extends DashboardBaseController{

	public function view($updated = false) {
		Loader::library('database_indexed_search');
		if ($this->post('reindex')) {
			IndexedSearch::clearSearchIndex();
			$this->redirect('/dashboard/system/seo/search_index', 'index_cleared');
		} else { 
			if($updated) {
				$this->set('message', t('Search Index Preferences Updated'));
			}
			if ($this->isPost()) {
				if($this->token->validate('update_search_index')) {
					$areas = $this->post('arHandle');
					
					if (!is_array($areas)) {
						$areas = array();
					}
					Config::save('SEARCH_INDEX_AREA_LIST', serialize($areas));
					Config::save('SEARCH_INDEX_AREA_METHOD', $this->post('SEARCH_INDEX_AREA_METHOD'));
					$this->redirect('/dashboard/system/seo/search_index', 'updated');
				} else {
					$this->set('error', array($this->token->getErrorMessage()));
				}
				
			}
			$areas = Area::getHandleList();
			$selectedAreas = array();
			$this->set('areas', $areas);
			$this->set('selectedAreas', IndexedSearch::getSavedSearchableAreas());
		}
	}
	
	public function index_cleared() {
		$this->set('message', t('Index cleared. You must now reindex your site from the Automated Jobs page.'));
		$this->view();
	}
}