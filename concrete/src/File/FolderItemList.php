<?php

namespace Concrete\Core\File;

use Closure;
use Concrete\Core\Permission\Access\Entity\FileUploaderEntity;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Key\FileFolderKey;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\FolderItemListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class FolderItemList extends AttributedItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $parent;
    protected $searchSubFolders = false;
    protected $permissionsChecker;

    protected $autoSortColumns = [
        'folderItemName',
        'folderItemModified',
        'folderItemType',
        'folderItemSize',
    ];

    public function __construct(StickyRequest $req = null)
    {
        $u = new \User();
        if ($u->isSuperUser()) {
            $this->ignorePermissions();
        }
        parent::__construct($req);
    }

    public function enableSubFolderSearch()
    {
        $this->searchSubFolders = true;
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
        $this->query->select('distinct n.treeNodeID')
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

    /**
     * Filter the files by their extension.
     *
     * @param string|string[] $extension one or more file extensions (with or without leading dot)
     */
    public function filterByExtension($extension)
    {
        $extensions = is_array($extension) ? $extension : [$extension];
        if (count($extensions) > 0) {
            $expr = $this->query->expr();
            $or = $expr->orX();
            foreach ($extensions as $extension) {
                $extension = ltrim((string) $extension, '.');
                $or->add($expr->eq('fv.fvExtension', $this->query->createNamedParameter($extension)));
                if ($extension === '') {
                    $or->add($expr->isNull('fv.fvExtension'));
                }
            }
            $this->query->andWhere($or);
        }
    }

    public function deliverQueryObject()
    {
        if (!isset($this->parent)) {
            $filesystem = new Filesystem();
            $this->parent = $filesystem->getRootFolder();
        }

        if ($this->searchSubFolders) {
            // determine how many subfolders are within the parent folder.
            $subFolders = $this->parent->getHierarchicalNodesOfType('file_folder', 1, false, true);
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

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        $u = new User();
        // Super user can search any files
        if (!$u->isSuperUser()) {
            /** @var FileFolderKey $pk */
            $pk = FileFolderKey::getByHandle('search_file_folder');
            if (is_object($pk)) {
                $pk->setPermissionObject($this->parent);
                /** @var \Concrete\Core\Permission\Access\Access $pa */
                $pa = $pk->getPermissionAccessObject();
                // Check whether or not current user can search files in the current folder
                if (is_object($pa) && $pa->validate()) {
                    // Get all access entities without "File Uploader" entity
                    $accessEntitiesWithoutFileUploader = [];
                    $accessEntities = $u->getUserAccessEntityObjects();
                    foreach ($accessEntities as $accessEntity) {
                        if (!$accessEntity instanceof FileUploaderEntity) {
                            $accessEntitiesWithoutFileUploader[] = $accessEntity;
                        }
                    }
                    /*
                     * For performance reason, if the current user can not search files without "File Uploader" entity,
                     * we filter only files that uploaded by the current user or permission overridden.
                     */
                    if (!$pa->validateAccessEntities($accessEntitiesWithoutFileUploader)) {
                        $query
                            ->leftJoin('tf', 'Files', 'f', 'tf.fID = f.fID')
                            ->andWhere('(f.uID = :fileUploaderID OR f.fOverrideSetPermissions = 1) OR nt.treeNodeTypeHandle != \'file\'')
                            ->setParameter('fileUploaderID', $u->getUserID());
                    }
                }
            }
        }

        return $query;
    }

    public function sortByNodeName()
    {
        $this->sortBy('folderItemName', 'asc');
    }

    public function sortByNodeType()
    {
        $this->sortBy('folderItemType', 'asc');
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\FileKey';
    }
}
