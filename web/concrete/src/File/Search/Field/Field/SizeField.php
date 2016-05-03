<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class SizeField extends AbstractField
{
    public function getKey()
    {
        return 'size';
    }

    public function getDisplayName()
    {
        return t('Size');
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = '';
        $html .= $form->text('size_from', $searchRequest['size_from']);
        $html .= t('to');
        $html .= $form->text('size_to', $searchRequest['size_to']);
        $html .= t('KB');
        return $html;
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list, $request)
    {
        $from = $request['size_from'];
        $to = $request['size_to'];
        $list->filterBySize($from, $to);
    }
}
