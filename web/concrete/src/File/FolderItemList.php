<?php
namespace Concrete\Core\File;

use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PermissionablePagination;
use Concrete\Core\Search\PermissionableListItemInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class FolderItemList extends ItemList implements PermissionableListItemInterface
{

    protected $parent;

    protected $autoSortColumns = array(
        'folderItemName',
        'folderItemModified',
        'folderItemSize'
    );

    public function __construct(FileFolder $parent)
    {
        $request = new StickyRequest('file_folder_' . $parent->getTreeNodeID());
        parent::__construct($request);
        $this->parent = $parent;
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\FileKey';
    }

    public function setPermissionsChecker(\Closure $checker)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function createQuery()
    {
        $this->query->select('n.treeNodeID')
            ->addSelect('if(nt.treeNodeTypeHandle=\'file\', fv.fvTitle, n.treeNodeName) as folderItemName')
            ->addSelect('if(nt.treeNodeTypeHandle=\'file\', fv.fvDateAdded, n.dateModified) as folderItemModified')
            ->addSelect('fv.fvSize as folderItemSize')
            ->from('TreeNodes', 'n')
            ->innerJoin('n', 'TreeNodeTypes', 'nt', 'nt.treeNodeTypeID = n.treeNodeTypeID')
            ->leftJoin('n', 'TreeFileNodes', 'tf', 'tf.treeNodeID = n.treeNodeID')
            ->leftJoin('tf', 'FileVersions', 'fv', 'tf.fID = fv.fID and fv.fvIsApproved = 1');

    }

    public function getTotalResults()
    {
        $u = new \User();
        if ($this->permissionsChecker === -1) {
            $query = $this->deliverQueryObject();

            return $query->select('count(distinct n.treeNodeID)')->setMaxResults(1)->execute()->fetchColumn();
        } else {
            return -1; // unknown
        }
    }

    protected function createPaginationObject()
    {
        $u = new \User();
        if ($this->permissionsChecker === -1) {
            $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
                $query->select('count(distinct n.treeNodeID)')->setMaxResults(1);
            });
            $pagination = new Pagination($this, $adapter);
        } else {
            $pagination = new PermissionablePagination($this);
        }

        return $pagination;
    }

    public function getResult($queryRow)
    {
        $f = Node::getByID($queryRow['treeNodeID']);
        if (is_object($f) && $this->checkPermissions($f)) {
            return $f;
        }
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            } else {
                return call_user_func_array($this->permissionsChecker, array($mixed));
            }
        }

        $fp = new \Permissions($mixed);
        return $fp->canViewTreeNode();
    }

    public function deliverQueryObject()
    {
        $this->query->andWhere('n.treeNodeParentID = :treeNodeParentID');
        $this->query->setParameter('treeNodeParentID', $this->parent->getTreeNodeID());
        return parent::deliverQueryObject();
    }

    public function sortByNodeName()
    {
        $this->sortBy('folderItemName', 'asc');
    }

}
