<?php
namespace Concrete\Controller\Search;

use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\User\Group\GroupList;

class Express extends Standard
{

    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make('\Concrete\Controller\Dialog\Express\AdvancedSearch');
    }

    protected function getSavedSearchPreset($presetID)
    {
        return false;
    }

    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('eKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }
        return $fields;
    }

    protected function loadEntity()
    {
        if (!isset($this->entity)) {
            $entity = \Express::getObjectByID($this->request->query->get('exEntityID'));
            $this->entity = $entity;
        }
    }

    protected function canAccess()
    {
        $this->loadEntity();
        $ep = new \Permissions($this->entity);
        return $ep->canViewExpressEntries();
    }


}
