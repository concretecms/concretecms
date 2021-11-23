<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Area\Area;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Search\IndexedSearch;

class Searchindex extends DashboardPageController
{
    public function view($updated = false)
    {
        $this->set('availableAreaIndexMethods', $this->getAvailableAreaIndexMethods());
        $this->set('areaIndexMethod', IndexedSearch::getSearchableAreaAction());
        $this->set('availableAreas', Area::getHandleList());
        $this->set('selectedAreas', IndexedSearch::getSavedSearchableAreas());
    }

    public function save()
    {
        $post = $this->request->request;
        $config = $this->app->make('config');
        if (!$this->token->validate('update_search_index')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $areaIndexMethod = $post->get('areaIndexMethod');
        if (!is_string($areaIndexMethod) || !array_key_exists($areaIndexMethod, $this->getAvailableAreaIndexMethods())) {
            $this->error->add(t('Please specify the indexing method.'));
        }
        $areas = $post->get('arHandle');
        if (!is_array($areas)) {
            $areas = [];
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config->save('concrete.misc.search_index_area_list', serialize($areas));
        $config->save('concrete.misc.search_index_area_method', $areaIndexMethod);
        $this->flash('success', t('Search Index Preferences Updated'));

        return $this->buildRedirect($this->action());
    }

    public function clear_index()
    {
        if (!$this->token->validate('clear_index')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }
        IndexedSearch::clearSearchIndex();
        $this->flash('success', t('Index cleared. You must now reindex your site from the Automated Jobs page.'));

        return $this->buildRedirect($this->action());
    }

    /**
     * @return string[]
     */
    protected function getAvailableAreas(): array
    {
        return IndexedSearch::getSavedSearchableAreas();
    }

    protected function getAvailableAreaIndexMethods(): array
    {
        return [
            'allowlist' => t('Allowlist - only use the selected areas below when searching content.'),
            'denylist' => t('Denylist - skip the selected areas below when searching content.'),
        ];
    }
}
