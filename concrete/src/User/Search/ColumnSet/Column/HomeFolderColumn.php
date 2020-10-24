<?php
namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class HomeFolderColumn extends Column implements PagerColumnInterface
{

    public function getColumnKey()
    {
        return 'u.uHomeFileManagerFolderID';
    }

    public function getColumnName()
    {
        return t('Home Folder');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\User\Search\ColumnSet\Available', 'getFolderName'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(u.uHomeFileManagerFolderID, u.uID) %s (:sortName, :sortID)', $sort);
        $query->setParameter('sortName', $mixed->getUserHomeFolderId());
        $query->setParameter('sortID', $mixed->getUserID());
        $query->andWhere($where);
    }

}
