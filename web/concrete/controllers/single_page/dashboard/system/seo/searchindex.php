<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Searchindex extends DashboardPageController{

	public function view($updated = false) {
		
		if ($this->post('reindex')) {
			IndexedSearch::clearSearchIndex();
			$this->redirect('/dashboard/system/seo/searchindex', 'index_cleared');
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
					Config::save('SEARCH_INDEX_AREA_METHOD', Loader::helper('security')->sanitizeString($this->post('SEARCH_INDEX_AREA_METHOD')));
					$this->redirect('/dashboard/system/seo/searchindex', 'updated');
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
