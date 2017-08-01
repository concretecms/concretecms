<?php
namespace Concrete\Core\File;

use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\FolderItemListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Search\Pagination\PermissionablePagination;
use Concrete\Core\Search\PermissionableListItemInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;
use Concrete\Core\Permission\Checker as Permissions;

class FolderItemList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $parent;
    protected $permissionsChecker;

    public function __construct(StickyRequest $req = null)
    {
        $u = new \User();
        if ($u->isSuperUser()) {
            $this->ignorePermissions();
        }
        parent::__construct($req);
    }

    protected $autoSortColumns = [
        'folderItemName',
        'folderItemModified',
        'folderItemType',
        'folderItemSize',
    ];

    /**
     * @return mixed
     */
    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    public function getPagerManager()
    {
        return new FolderItemListPagerManager($this);
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\FileKey';
    }

    public function setPermissionsChecker(Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
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
            ->addSelect('case when nt.treeNodeTypeHandle=\'search_preset\' then 1 when nt.treeNodeTypeHandle=\'file_folder\' then 2 else (10 + fvType) end as folderItemType')
            ->addSelect('fv.fvSize as folderItemSize')
            ->from('TreeNodes', 'n')
            ->innerJoin('n', 'TreeNodeTypes', 'nt', 'nt.treeNodeTypeID = n.treeNodeTypeID')
            ->leftJoin('n', 'TreeFileNodes', 'tf', 'tf.treeNodeID = n.treeNodeID')
            ->leftJoin('tf', 'FileVersions', 'fv', 'tf.fID = fv.fID and fv.fvIsApproved = 1');
    }

    public function getTotalResults()
    {
        if (isset($this->permissionsChecker) && $this->permissionsChecker === -1) {
            $query = $this->deliverQueryObject();

            return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct n.treeNodeID)')->setMaxResults(1)->execute()->fetchColumn();
        } else {
            return -1; // unknown
        }
    }

    public function getPaginationAdapter()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct n.treeNodeID)')->setMaxResults(1);
        });
        return $adapter;
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
                return call_user_func_array($this->permissionsChecker, [$mixed]);
            }
        }

        $fp = new Permissions($mixed);

        return $fp->canViewTreeNode();
    }

    public function filterByParentFolder(FileFolder $folder)
    {
        $this->parent = $folder;
    }

    public function filterByType($type)
    {
        $this->query->andWhere('fv.fvType = :fvType or fvType is null');
        $this->query->setParameter('fvType', $type);
    }

    public function deliverQueryObject()
    {
        if (!isset($this->parent)) {
            $filesystem = new Filesystem();
            $this->parent = $filesystem->getRootFolder();
        }
        $this->query->andWhere('n.treeNodeParentID = :treeNodeParentID');
        $this->query->setParameter('treeNodeParentID', $this->parent->getTreeNodeID());

        return parent::deliverQueryObject();
    }

    public function sortByNodeName()
    {
        $this->sortBy('folderItemName', 'asc');
    }

    public function sortByNodeType()
    {
        $this->sortBy('folderItemType', 'asc');
    }
}
