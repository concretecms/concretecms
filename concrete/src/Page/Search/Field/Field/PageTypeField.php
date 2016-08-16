<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\File\Type\Type;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\FieldInterface;
use Concrete\Core\Search\ItemList\ItemList;

class PageTypeField extends AbstractField
{

    protected $requestVariables = [
        'ptID'
    ];

    public function getKey()
    {
        return 'page_type';
    }

    public function getDisplayName()
    {
        return t('Page Type');
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $list->filterByPageTypeID($this->data['ptID']);
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = $form->select('ptID', array_reduce(
            \PageType::getList(), function ($types, $type) {
            $types[$type->getPageTypeID()] = $type->getPageTypeDisplayName();

            return $types;
        }
        ), $this->data['ptID']);
        return $html;
    }


}
