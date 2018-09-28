<?php

namespace Concrete\Controller\Search;

use Concrete\Controller\Dialog\User\AdvancedSearch;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Doctrine\ORM\EntityManagerInterface;

class Users extends Standard
{
    /**
     * @return \Concrete\Controller\Dialog\Search\AdvancedSearch
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make(AdvancedSearch::class);
    }

    /**
     * @param int $presetID
     *
     * @return \Concrete\Core\Entity\Search\SavedUserSearch|null
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManagerInterface::class);

        return $em->find(SavedUserSearch::class, $presetID);
    }

    /**
     * @return KeywordsField[]
     */
    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('uKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }

        return $fields;
    }

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }
}
