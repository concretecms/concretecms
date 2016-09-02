<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\Group\GroupList;

class UserGroupField extends AbstractField
{

    protected $requestVariables = [
        'gID'
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
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $filterGIDs = array();
        if (isset($this->data['gID']) && is_array($this->data['gID'])) {
            foreach ($this->data['gID'] as $gID) {
                $g = \Group::getByID($gID);
                if (is_object($g)) {
                    $gp = new \Permissions($g);
                    if ($gp->canSearchUsersInGroup()) {
                        $filterGIDs[] = $g->getGroupID();
                    }
                }
            }
        }
        foreach ($filterGIDs as $gID) {
            $list->filterByGroupID($gID);
        }
    }

    public function renderSearchField()
    {
        $gl = new GroupList();
        $g1 = $gl->getResults();
        $html = '<select multiple name="gID[]" class="selectize-select">';
        foreach ($g1 as $g) {
            $gp = new \Permissions($g);
            if ($gp->canSearchUsersInGroup($g)) {
                $html .= '<option value="' . $g->getGroupID() . '" ';
                if (is_array($this->data['gID']) && in_array($g->getGroupID(), $this->data['gID'])) {
                    $html .= 'selected="selected" ';
                }
                $html .= '>' . $g->getGroupDisplayName() . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }


}
