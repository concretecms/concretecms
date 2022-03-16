<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\UserList;

class IsActiveField extends AbstractField
{

    protected $requestVariables = [
        'active',
    ];

    public function getKey()
    {
        return 'is_active';
    }

    public function getDisplayName()
    {
        return t('Activated');
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = $form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), $this->getData('active'), array('style' => 'vertical-align: middle'));
        return $html;

    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        if ($this->getData('active') === '0') {
            $list->filterByIsActive(0);
        } elseif ($this->getData('active') === '1') {
            $list->filterByIsActive(1);
        }
    }



}
