<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class VersionAuthorField extends AbstractField
{
    protected $requestVariables = [
        'version_author',
    ];

    public function getKey()
    {
        return 'version_author';
    }

    public function getDisplayName()
    {
        return t('Approved Version Author');
    }

    public function filterList(ItemList $list)
    {
        $uID = (int) $this->getData('version_author');
        if ($uID > 0) {
            $list->filterByVersionAuthorUserID($uID);
        }
    }

    public function renderSearchField()
    {
        $userSelector = \Core::make(UserSelector::class);

        // selectUser() expects false (not null or 0) to mean "no current selection".
        return $userSelector->selectUser('version_author', $this->getData('version_author') ?: false);
    }
}
