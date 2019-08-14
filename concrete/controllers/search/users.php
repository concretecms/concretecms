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
     * @since 8.0.0
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make(AdvancedSearch::class);
    }

    /**
     * @param int $presetID
     *
     * @return \Concrete\Core\Entity\Search\SavedUserSearch|null
     * @since 8.0.0
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManagerInterface::class);

        return $em->find(SavedUserSearch::class, $presetID);
    }

    /**
     * @return KeywordsField[]
     * @since 8.0.0
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
     * @since 8.0.0
     */
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }
}
