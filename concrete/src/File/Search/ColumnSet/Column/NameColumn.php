<?php

namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Type\FileFolder;

class NameColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    public function getColumnKey()
    {
        return 'name';
    }

    public function getColumnName()
    {
        return t('Name');
    }

    public function getColumnCallback()
    {
        return ['\Concrete\Core\File\Search\ColumnSet\Available', 'getName'];
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $name = '';
        if ($mixed->getTreeNodeDisplayName()) {
            $name = $mixed->getTreeNodeDisplayName();
        }
        $config = Application::getFacadeApplication()->make('config');
        if ($config->get('concrete.file_manager.keep_folders_on_top')) {
            $where = sprintf('(if(nt.treeNodeTypeHandle=\'file\', concat("1", fv.fvTitle), concat("0", n.treeNodeName)), n.treeNodeID) %s (:sortName, :sortID)', $sort);
            if ($mixed instanceof FileFolder) {
                $name = '0' . $name;
            } else {
                $name = '1' . $name;
            }
        } else {
            $where = sprintf('(if(nt.treeNodeTypeHandle=\'file\', fv.fvTitle, n.treeNodeName), n.treeNodeID) %s (:sortName, :sortID)', $sort);
        }
        $query->setParameter('sortName', $name);
        $query->setParameter('sortID', $mixed->getTreeNodeID());
        $this->andWhereNotExists($query, $where);
    }
}
