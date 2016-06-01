<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Area;
use Concrete\Core\Page\Search\IndexedSearch;

class Searchindex extends DashboardPageController
{
    public function view($updated = false)
    {
        if ($this->post('reindex')) {
            IndexedSearch::clearSearchIndex();
            $this->redirect('/dashboard/system/seo/searchindex', 'index_cleared');
        } else {
            if ($updated) {
                $this->set('message', t('Search Index Preferences Updated'));
            }
            if ($this->isPost()) {
                if ($this->token->validate('update_search_index')) {
                    $areas = $this->post('arHandle');

                    if (!is_array($areas)) {
                        $areas = array();
                    }
                    Config::save('concrete.misc.search_index_area_list', serialize($areas));
                    Config::save('concrete.misc.search_index_area_method', Loader::helper('security')->sanitizeString($this->post('SEARCH_INDEX_AREA_METHOD')));
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

    public function index_cleared()
    {
        $this->set('message', t('Index cleared. You must now reindex your site from the Automated Jobs page.'));
        $this->view();
    }
}
