<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Page\Page;
use Concrete\Core\Search\Column\Column;

class PageTemplateColumn extends Column
{

    public function getColumnName()
    {
        return t('Page Template');
    }

    public function getColumnKey()
    {
        return 'pageTemplate';
    }

    /**
     * @param Page $page
     * @return false|mixed|void
     */
    public function getColumnValue($page)
    {
        $template = $page->getPageTemplateObject();
        if ($template) {
            return $template->getPageTemplateDisplayName();
        }
        return '';
    }

    public function isColumnSortable()
    {
        return false;
    }

}
