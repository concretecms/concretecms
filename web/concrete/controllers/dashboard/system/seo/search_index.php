<?
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSystemSeoSearchIndexController extends DashboardBaseController{

	public function view($updated = false) {
		Loader::library('database_indexed_search');
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