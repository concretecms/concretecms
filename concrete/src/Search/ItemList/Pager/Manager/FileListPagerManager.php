<?php
namespace Concrete\Core\Search\ItemList\Pager\Manager;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\File\File;

class FileListPagerManager extends AbstractPagerManager
{

    public function getCursorStartValue($mixed)
    {
        return $mixed->getFileID();
    }

    public function getCursorObject($cursor)
    {
        return File::getByID($cursor);
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function sortListByCursor(PagerProviderInterface $itemList, $direction)
    {
        $itemList->getQueryObject()->addOrderBy('f.fID', $direction);
    }



}