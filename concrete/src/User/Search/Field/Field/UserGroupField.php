<?php

namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\UserList;

class UserGroupField extends AbstractField
{
    protected $requestVariables = [
        'gID', 'uGroupIn',
    ];

    public function getKey()
    {
        return 'user_group';
    }

    public function getDisplayName()
    {
        return t('User Group');
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $filterGroups = [];
        if (isset($this->data['gID']) && is_array($this->getData('gID'))) {
            foreach ($this->getData('gID') as $gID) {
                $g = \Group::getByID($gID);
                if (is_object($g)) {
                    $gp = new \Permissions($g);
                    if ($gp->canSearchUsersInGroup()) {
                        $filterGroups[] = $g;
                    }
                }
            }
        }
        $inGroup = true;
        if ($this->getData('uGroupIn') == 'not') {
            $inGroup = false;
        }
        $list->filterByInAnyGroup($filterGroups, $inGroup);
    }

    public function renderSearchField()
    {
        $identifier = app()->make('helper/validation/identifier')->getString(12);
        $gl = new GroupList();
        $g1 = $gl->getResults();
        $html = '<div class="form-group"><select multiple name="gID[' . $identifier . '][]" class="ccm-enhanced-select">';

        foreach ($g1 as $g) {
            $gp = new \Permissions($g);
            if ($gp->canSearchUsersInGroup($g)) {
                $html .= '<option value="' . $g->getGroupID() . '" ';

                if (is_array($this->getData('gID')) && in_array($g->getGroupID(), $this->getData('gID'))) {
                    $html .= 'selected="selected" ';
                }
                $html .= '>' . $g->getGroupDisplayName() . '</option>';
            }
        }

        $html .= '</select></div><br/>';

        $html .= '<div class="form-group"><select name="uGroupIn[' . $identifier . ']" class="form-select">';
        $html .= '<option value="in"' . ($this->getData('uGroupIn') == 'in' ? ' selected' : '') . '>' . t('Search for users in group(s)') . '</option>';
        $html .= '<option value="not"' . ($this->getData('uGroupIn') == 'not' ? ' selected' : '') . '>' . t('Search for users not included in group(s)') . '</option>';
        $html .= '</select></div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::loadDataFromRequest()
     */
    public function loadDataFromRequest(array $request)
    {
        if (!$this->isLoaded) {
            $fields = [];

            if (isset($request['gID']) && count($request['gID'])) {
                $uGroupIns = $request['uGroupIn'];
                $values = ['in' => [], 'not' => []];

                foreach ($request['gID'] as $index => $groupIDs) {
                    $values[$uGroupIns[$index]] = array_merge($values[$uGroupIns[$index]], $groupIDs);
                }

                foreach ($values as $uGroupIn => $groupArray) {
                    if (!count($groupArray)) {
                        continue;
                    }

                    $field = clone($this);
                    $field->setData('uGroupIn', $uGroupIn);
                    $vals = array_values(array_unique($groupArray));
                    $field->setData('gID', $vals);
                    $fields[] = $field;
                }

                $this->isLoaded = true;

                return $fields;
            }
        }

        return $this;
    }
}
