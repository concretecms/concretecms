<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Page\PageList;
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Field\FieldInterface::filterList()
     *
     * @param PageList $list
     */
    public function filterList(ItemList $list)
    {
        $owner = $this->getData('owner');
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
        return $form->text('owner', $this->getData('owner'));
    }


}
