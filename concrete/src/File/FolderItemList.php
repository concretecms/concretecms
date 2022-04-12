<?php

namespace Concrete\Core\File;

use Closure;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Permission\Access\Entity\FileUploaderEntity;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Permission\Key\FileFolderKey;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\FolderItemListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class FolderItemList extends AttributedItemList implements PagerProviderInterface, PaginationProviderInterface
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
        'dateModified',
        'type',
        'size',
        'f.fID',
        'fv.fvFilename',
        'fv.fvTitle',
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

    public function createQuery()
    {
        $this->query->select('distinct n.treeNodeID')
            ->addSelect('if(nt.treeNodeTypeHandle=\'file\', fv.fvTitle, n.treeNodeName) as name')
            ->addSelect('if(nt.treeNodeTypeHandle=\'file\', fv.fvDateAdded, n.dateModified) as dateModified')
            ->addSelect('case when nt.treeNodeTypeHandle=\'file_folder\' then 1 else (10 + fvType) end as type')
            ->addSelect('fv.fvSize as size')
            ->from('TreeNodes', 'n')
            ->innerJoin('n', 'TreeNodeTypes', 'nt', 'nt.treeNodeTypeID = n.treeNodeTypeID')
            ->leftJoin('n', 'TreeFileNodes', 'tf', 'tf.treeNodeID = n.treeNodeID')
            ->leftJoin('tf', 'FileVersions', 'fv', 'tf.fID = fv.fID and fv.fvIsApproved = 1')
            ->leftJoin('fv', 'Files', 'f', 'fv.fID = f.fID')
            ->leftJoin('f', 'Users', 'u', 'f.uID = u.uID')
            ->leftJoin('f', 'FileSearchIndexAttributes', 'fsi', 'f.fID = fsi.fID')
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

        $fp = new Permissions($mixed);

        return $fp->canViewTreeNode();
    }

    public function filterByParentFolder(FileFolder $folder)
    {
        $this->parent = $folder;
    }

    public function filterByType($type)
    {
        $this->query->andWhere('fv.fvType = :fvType');
        $this->query->setParameter('fvType', $type);
    }

    /**
     * Filters by public date.
     *
     * @param string $date
     * @param string $comparison
     */
    public function filterByDateAdded($date, $comparison = '=')
    {
        $this->query->andWhere($this->query->expr()->comparison(
            'f.fDateAdded',
            $comparison,
            $this->query->createNamedParameter($date)
        ));
    }

    public function filterByAddedToPageID($ocID)
    {
        $this->query->leftJoin('f', 'FileUsageRecord', 'fu', 'f.fID = fu.file_id');
        $this->query->andWhere('fu.collection_id = :ocID');
        $this->query->setParameter('ocID', $ocID);
    }

    /**
     * Filter the files by their storage location using a storage location id.
     *
     * @param int $fslID storage location id
     */
    public function filterByStorageLocationID($fslID)
    {
        $fslID = (int) $fslID;
        $this->query->andWhere('f.fslID = :fslID');
        $this->query->setParameter('fslID', $fslID);
    }

    /**
     * Filter the files by their storage location using a storage location object.
     *
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation|int $storageLocation storage location object
     */
    public function filterByStorageLocation($storageLocation)
    {
        if ($storageLocation instanceof \Concrete\Core\Entity\File\StorageLocation\StorageLocation) {
            $this->filterByStorageLocationID($storageLocation->getID());
        } elseif (!is_object($storageLocation)) {
            $this->filterByStorageLocationID($storageLocation);
        } else {
            throw new \Exception(t('Invalid file storage location.'));
        }
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

    public function filterBySet($fs)
    {
        $table = 'fsf' . $fs->getFileSetID();
        $this->query->leftJoin('f', 'FileSetFiles', $table, 'f.fID = ' . $table . '.fID');
        $this->query->andWhere($table . '.fsID = :fsID' . $fs->getFileSetID());
        $this->query->setParameter('fsID' . $fs->getFileSetID(), $fs->getFileSetID());
    }

    /**
     * Filters the file list by file size (in kilobytes).
     *
     * @param int|float $from
     * @param int|float $to
     */
    public function filterBySize($from, $to)
    {
        if ($from > 0) {
            $this->query->andWhere('fv.fvSize >= :fvSizeFrom');
            $this->query->setParameter('fvSizeFrom', $from * 1024);
        }
        if ($to > 0) {
            $this->query->andWhere('fv.fvSize <= :fvSizeTo');
            $this->query->setParameter('fvSizeTo', $to * 1024);
        }
    }

    /**
     * Filters by "keywords" (which searches everything including filenames,
     * title, folder names, etc....
     *
     * @param string $keywords
     */
    public function filterByKeywords($keywords)
    {
        $expressions = [
            $this->query->expr()->like('fv.fvFilename', ':keywords'),
            $this->query->expr()->like('fv.fvDescription', ':keywords'),
            $this->query->expr()->like('treeNodeName', ':keywords'),
            $this->query->expr()->like('fv.fvTags', ':keywords'),
            $this->query->expr()->eq('uName', ':keywords'),
        ];
        $keys = FileKey::getSearchableIndexedList();
        foreach ($keys as $ak) {
            $cnt = $ak->getController();
            $expressions[] = $cnt->searchKeywords($keywords, $this->query);
        }

        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    public function deliverQueryObject()
    {
        $filesystem = new Filesystem();
        $rootFolder = $filesystem->getRootFolder();

        if (!isset($this->parent)) {
            $this->parent = $rootFolder;
        }

        // If there is no parent set, OR if we are set to search the root of the site and all sub-folders (which
        // effectively means that there SHOULD be no parents set, then we simply return the deliveryQueryObject
        // of the parent, because there is no need for any further filtering.
        if (isset($this->parent) &&
            $this->parent->getTreeNodeID() == $rootFolder->getTreeNodeID() && $this->searchSubFolders) {
            // Before we can simply return, however, we need to ensure we're only returning nodes that the
            // file manager cares about.
            $this->query->andWhere(
                $this->query->expr()->in('nt.treeNodeTypeHandle', array_map([$this->query->getConnection(), 'quote'], ['file', 'file_folder']))
            );
            // if we don't add this we're gonna see the File Manager node.
            $this->query->andWhere('n.treeNodeParentID > 0');

            return parent::deliverQueryObject();
        }

        // If we've gotten down here, we have a parent object.

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
        $u = Application::getFacadeApplication()->make(User::class);
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
                            ->setParameter('fileUploaderID', $u->getUserID())
                        ;
                    }
                }
            }
        }

        return $query;
    }

    public function sortByNodeName()
    {
        $this->sortBy('name', 'asc');
    }

    public function sortByNodeType()
    {
        $this->sortBy('type', 'asc');
    }

    protected function getAttributeKeyClassName()
    {
        return '\\Concrete\\Core\\Attribute\\Key\\FileKey';
    }
}
