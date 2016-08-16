<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class PageOwnerField extends AbstractField
{

    protected $requestVariables = [
        'owner',
    ];

    public function getKey()
    {
        return 'owner';
    }

    public function getDisplayName()
    {
        return t('Page Owner');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $owner = $this->data['owner'];
        $ui = \UserInfo::getByUserName($owner);
        if (is_object($ui)) {
            $list->filterByUserID($ui->getUserID());
        } else {
            $list->filterByUserID(-1);
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        return $form->text('owner', $this->data['owner']);
    }


}
