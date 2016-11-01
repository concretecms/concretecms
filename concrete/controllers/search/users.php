<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\User\Group\GroupList;

class Users extends Standard
{

    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make('\Concrete\Controller\Dialog\User\AdvancedSearch');
    }

    protected function getSavedSearchPreset($presetID)
    {
        $em = \Database::connection()->getEntityManager();
        $preset = $em->find('Concrete\Core\Entity\Search\SavedUserSearch', $presetID);
        return $preset;
    }

    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('uKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }
        return $fields;
    }

    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');
        if ($dh->canAccessUserSearchInterface()) {
            return true;
        }
        return false;
    }


}
