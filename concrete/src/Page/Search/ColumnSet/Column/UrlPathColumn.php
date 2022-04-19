<?php
namespace Concrete\Core\Page\Search\ColumnSet\Column;

use Concrete\Core\Page\Page;
use Concrete\Core\Search\Column\Column;

class UrlPathColumn extends Column
{

    public function getColumnName()
    {
        return t('URL Path');
    }

    public function getColumnKey()
    {
        return 'urlPath';
    }

    /**
     * @param Page $page
     * @return false|mixed|void
     */
    public function getColumnValue($page)
    {
        $path = $page->getCollectionPath();
        return $path;
    }

    public function isColumnSortable()
    {
        return false;
    }

}
