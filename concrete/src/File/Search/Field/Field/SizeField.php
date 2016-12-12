<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\File\FileList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class SizeField extends AbstractField
{

    protected $requestVariables = ['size_from', 'size_to'];

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
        $html .= $form->number('size_from', $this->data['size_from'], array('min' => 0));
        $html .= t('to');
        $html .= $form->number('size_to', $this->data['size_to'], array('min' => 1));
        $html .= t('KB');
        return $html;
    }

    /**
     * @param FileList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $from = $this->data['size_from'];
        $to = $this->data['size_to'];
        $list->filterBySize($from, $to);
    }
}
