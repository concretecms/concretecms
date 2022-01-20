<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;

class IncludePageAliasesField extends AbstractField
{
    protected $requestVariables = [
        'includeAliases',
    ];

    public function getKey()
    {
        return 'include_page_aliases';
    }

    public function getDisplayName()
    {
        return t('Include Page Aliases');
    }

    /**
     * @param \Concrete\Core\Page\PageList $list
     */
    public function filterList(ItemList $list)
    {
        if ($this->getData('includeAliases') === '1') {
            $list->includeAliases();
        }
    }

    public function renderSearchField()
    {
        $form = \Core::make('helper/form');
        $html = '<div>';
        $html .= '<div class="form-check">' . $form->radio('includeAliases', 0, $this->getData('includeAliases')) . $form->label('includeAliases'.'1',t('No'), ['class'=>'form-check-label']) . '</div>';
        $html .= '<div class="form-check">' . $form->radio('includeAliases', 1, $this->getData('includeAliases')) . $form->label('includeAliases'.'2',t('Yes'), ['class'=>'form-check-label']) . '</div>';
        $html .= '</div>';
        return $html;
    }
}
