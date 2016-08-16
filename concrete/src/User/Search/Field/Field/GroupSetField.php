<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\Group\GroupSet;
use Concrete\Core\User\Group\GroupSetList;
use Concrete\Core\User\UserList;

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
        $groupsets = array();
        foreach ($gsl->get() as $gs) {
            $groupsets[$gs->getGroupSetID()] = $gs->getGroupSetDisplayName();
        }
        $html = $form->select('gsID', $groupsets);
        return $html;
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $gsID = $this->data['gsID'];
        $gs = GroupSet::getByID($gsID);
        $groupsetids = array(-1);
        if (is_object($gs)) {
            $groups = $gs->getGroups();
        }
        $list->addToQuery('left join UserGroups ugs on u.uID = ugs.uID');
        $pk = Key::getByHandle('search_users_in_group');
        foreach ($groups as $g) {
            if ($pk->validate($g) && (!in_array($g->getGroupID(), $groupsetids))) {
                $groupsetids[] = $g->getGroupID();
            }
        }
        $instr = 'ugs.gID in (' . implode(',', $groupsetids) . ')';
        $list->filter(false, $instr);
    }



}
