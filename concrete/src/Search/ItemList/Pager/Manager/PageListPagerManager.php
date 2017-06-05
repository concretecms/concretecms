<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Page\Page;
use Concrete\Core\Search\ItemList\Column;

class PageListPagerManager extends AbstractPagerManager
{

    /**
     * @param Column $column
     * @param $mixed Page
     */
    public function getNextValue(Column $column, $mixed)
    {
        switch ($column->getKey()) {
            case 'cvDatePublic':
                return $mixed->getCollectionDatePublic();
        }
    }


}