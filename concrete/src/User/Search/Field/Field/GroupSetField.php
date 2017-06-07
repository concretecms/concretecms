<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\Group\GroupSet;
use Concrete\Core\User\Group\GroupSetList;
use Concrete\Core\User\UserList;
use Permissions;

class GroupSetField extends AbstractField
{
    protected $requestVariables = [
        'gsID',
    ];

    public function getKey()
    {
        return 'group_set';
    }

    public function getDisplayName()
    {
        return t('Group Set');
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $gsl = new GroupSetList();
        $groupsets = [];
        foreach ($gsl->get() as $gs) {
            $groupsets[$gs->getGroupSetID()] = $gs->getGroupSetDisplayName();
        }
        $html = $form->select('gsID', $groupsets);

        return $html;
    }

    /**
     * @param UserList $list
     */
    public function filterList(ItemList $list)
    {
        $accessibleGroups = [];
        $groupSetID = isset($this->data['gsID']) ? $this->data['gsID'] : null;
        $groupSet = $groupSetID ? GroupSet::getByID($groupSetID) : null;
        if ($groupSet) {
            foreach ($groupSet->getGroups() as $group) {
                $groupPermissions = new Permissions($group);
                if ($groupPermissions->canSearchUsersInGroup()) {
                    $accessibleGroups[] = $group;
                }
            }
        }
        $list->filterByInAnyGroup($accessibleGroups);
    }
}
