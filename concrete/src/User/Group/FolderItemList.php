<?php

namespace Concrete\Core\User\Group;

use Closure;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\ItemList\Database\ItemList as DatabaseItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\FolderItemListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class FolderItemList extends DatabaseItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $parent;

    protected $searchSubFolders = false;

    protected $permissionsChecker;

    /**
     * Determines whether the list should automatically always sort by a column that's in the automatic sort.
     * This is the default, but it's better to be able to use the AutoSortColumnRequestModifier on a search
     * result class instead. In order to do that we disable the auto sort here, while still providing the array
     * of possible auto sort columns.
     *
     * @var bool
     */
    protected $enableAutomaticSorting = false;

    protected $autoSortColumns = [
        'name',
        'type'
    ];

    public function __construct()
    {
        $u = Application::getFacadeApplication()->make(User::class);
        if ($u->isSuperUser()) {
            $this->ignorePermissions();
        }
        parent::__construct();
    }

    public function enableSubFolderSearch()
    {
        $this->searchSubFolders = true;
    }

    public function enableAutomaticSorting()
    {
        $this->enableAutomaticSorting = true;
    }

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

    public function setPermissionsChecker(?Closure $checker = null)
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

    public function filterByName($gName)
    {
        $this->query->andWhere('g.gName LIKE :gName');
        $this->query->setParameter('gName', "%" . $gName . "%");
    }

    public function createQuery()
    {
        $this->query->select('distinct n.treeNodeID')
            ->addSelect('if(nt.treeNodeTypeHandle=\'group\', g.gName, n.treeNodeName) as name')
            ->addSelect('if(nt.treeNodeTypeHandle=\'group_folder\', 1, 10 + gt.gtID) as type')
            ->from('TreeNodes', 'n')
            ->innerJoin('n', 'TreeNodeTypes', 'nt', 'nt.treeNodeTypeID = n.treeNodeTypeID')
            ->leftJoin('n', 'TreeGroupNodes', 'tf', 'tf.treeNodeID = n.treeNodeID')
            ->leftJoin('n', $this->query->getConnection()->getDatabasePlatform()->quoteSingleIdentifier('Groups'), 'g', 'tf.gID = g.gID')
            ->leftJoin('n', 'GroupTypes', 'gt', 'g.gtID = gt.gtID')
            ->andWhere("nt.treeNodeTypeHandle='group_folder' OR nt.treeNodeTypeHandle='group' AND g.gID")
        ;
    }

    public function getTotalResults()
    {
        if (isset($this->permissionsChecker) && $this->permissionsChecker === -1) {
            $query = $this->deliverQueryObject();

            return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct n.treeNodeID)')->setMaxResults(1)->execute()->fetchColumn();
        }

        return -1; // unknown
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
            }

            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        $fp = new Checker($mixed);

        /** @noinspection PhpUndefinedMethodInspection */
        return $fp->canViewTreeNode();
    }

    /**
     * @param GroupFolder|Node $folder
     */
    public function filterByParentFolder(Node $folder)
    {
        $this->parent = $folder;
    }

    /**
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        if (strlen($keywords) > 0) {
            $this->searchSubFolders = true;
            $expressions = [
                $this->query->expr()->like('g.gName', ':keywords'),
                $this->query->expr()->like('n.treeNodeName', ':keywords'),
            ];
            $expr = $this->query->expr();
            $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
            $this->query->setParameter('keywords', '%' . $keywords . '%');
        }
    }

    public function deliverQueryObject()
    {
        $folderManager = new FolderManager();
        $rootFolder = $folderManager->getRootFolder();

        if (!isset($this->parent)) {
            $this->parent = $rootFolder;
        }

        // If there is no parent set, OR if we are set to search the root of the site and all sub-folders (which
        // effectively means that there SHOULD be no parents set, then we simply return the deliveryQueryObject
        // of the parent, because there is no need for any further filtering.
        if (isset($this->parent) &&
            $this->parent->getTreeNodeID() == $rootFolder->getTreeNodeID() && $this->searchSubFolders) {
            // Before we can simply return, however, we need to ensure we're only returning nodes that the
            // group folder manager cares about.
            $this->query->andWhere(
                $this->query->expr()->in('nt.treeNodeTypeHandle', array_map([$this->query->getConnection(), 'quote'], ['group', 'group_folder']))
            );
            // if we don't add this we're gonna see the Group Folder Manager node.
            $this->query->andWhere('n.treeNodeParentID > 0');

            return parent::deliverQueryObject();
        }

        // If we've gotten down here, we have a parent object.

        if ($this->searchSubFolders) {
            // determine how many subfolders are within the parent folder.
            $subFolders = $this->parent->getHierarchicalNodesOfType('group_folder', 1, false, true);
            $subFolderIds = [];
            foreach ($subFolders as $subFolder) {
                $subFolderIds[] = $subFolder['treeNodeID'];
            }
            $this->query->andWhere(
                $this->query->expr()->in('n.treeNodeParentID', array_map([$this->query->getConnection(), 'quote'], $subFolderIds))
            );
        } else {

            $this->query->andWhere('n.treeNodeParentID = :treeNodeParentID');
            $this->query->setParameter('treeNodeParentID', $this->parent->getTreeNodeID());

        }

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
