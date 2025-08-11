<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class IncludeSystemPagesField extends AbstractField
{
    protected $requestVariables = [
        'includeSystemPages',
    ];

    public function getKey()
    {
        return 'include_system_pages';
    }

    public function getDisplayName()
    {
        return t('Include System Pages');
    }

    /**
     * @param \Concrete\Core\Page\PageList $list
     */
    public function filterList(ItemList $list)
    {
        if ($this->getData('includeSystemPages') === '1') {
            $list->includeSystemPages();
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = '<div>';
        $html .= '<div class="form-check">' . $form->radio('includeSystemPages', 0, $this->getData('includeSystemPages')) . $form->label('includeSystemPages'.'1',t('No'), ['class'=>'form-check-label']) . '</div>';
        $html .= '<div class="form-check">' . $form->radio('includeSystemPages', 1, $this->getData('includeSystemPages')) . $form->label('includeSystemPages'.'2',t('Yes'), ['class'=>'form-check-label']) . '</div>';
        $html .= '</div>';
        return $html;
    }
}
