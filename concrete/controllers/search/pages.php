<?php
namespace Concrete\Controller\Search;

use Concrete\Controller\Dialog\Search\AdvancedSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Page\Search\Field\Field\SiteLocaleField;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Tree\Node\Type\SearchPreset;

class Pages extends Standard
{

    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make('\Concrete\Controller\Dialog\Page\AdvancedSearch');
    }

    protected function getSavedSearchPreset($presetID)
    {
        $em = \Database::connection()->getEntityManager();
        $preset = $em->find('Concrete\Core\Entity\Search\SavedPageSearch', $presetID);
        return $preset;
    }

    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = $this->request->get('cKeywords');
        $localeID = $this->request->get('localeID');
        if (is_string($keywords) && $keywords !== '') {
            $fields[] = new KeywordsField($keywords);
        }
        if (is_string($localeID) && $localeID !== '') {
            $fields[] = new SiteLocaleField($localeID);
        }
        return $fields;
    }

    protected function canAccess()
    {
        $permissions = new \Permissions();
        return $permissions->canAccessSitemap();
    }


}
