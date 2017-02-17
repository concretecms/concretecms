<?php
namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\UserList;

class IsValidatedField extends AbstractField
{
    protected $requestVariables = [
        'validated',
    ];

    public function getKey()
    {
        return 'is_validated';
    }

    public function getDisplayName()
    {
        return t('Validated');
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = $form->select('validated', array('0' => t('Unvalidated Users'), '1' => t('Validated Users')), array('style' => 'vertical-align: middle'));
        return $html;
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        if ($this->data['validated'] === '0') {
            $list->filterByIsValidated(0);
        } elseif ($this->data['validated'] === '1') {
            $list->filterByIsValidated(1);
        }
    }
}
