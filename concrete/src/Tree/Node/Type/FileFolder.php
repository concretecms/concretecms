<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\FileFolderMenu;
use Concrete\Core\User\User;
use Gettext\Translations;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class FileFolder extends TreeNode
{
    /**
     * @return int|null
     */
    protected $fslID = null;

    public function getTreeNodeTranslationContext()
    {
        return 'TreeNodeFileFolderName';
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\FileFolderResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\FileFolderAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'file_folder';
    }

    public function getTreeNodeTypeName()
    {
        return t('Folder');
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->getTreeNodeName() !== null) {
            $name = tc($this->getTreeNodeTranslationContext(), $this->getTreeNodeName());
            switch ($format) {
                case 'html':
                    return h($name);
                case 'text':
                default:
                    return $name;
            }
        } elseif ($this->getTreeNodeParentID() == 0) {
            return t('Folders');
        }
    }

    /**
     * Returns the storage location id of the folder.
     *
     * @return int
     */
    public function getTreeNodeStorageLocationID()
    {
        return (int) $this->fslID;
    }

    /**
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    public function getTreeNodeStorageLocationObject()
    {
        $app = Application::getFacadeApplication();

        return $app->make(StorageLocationFactory::class)->fetchByID($this->getTreeNodeStorageLocationID());
    }

    /**
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation|int $storageLocation Storage location object or id
     */
    public function setTreeNodeStorageLocation($storageLocation)
    {
        if ($storageLocation instanceof \Concrete\Core\Entity\File\StorageLocation\StorageLocation) {
            $this->setTreeNodeStorageLocationID($storageLocation->getID());
        } elseif (!is_object($storageLocation)) {
            $this->setTreeNodeStorageLocationID($storageLocation);
        } else {
            throw new Exception(t('Invalid file storage location.'));
        }
    }

    /**
     * @param int $fslID Storage location id
     */
    public function setTreeNodeStorageLocationID($fslID)
    {
        $app = Application::getFacadeApplication();
        $location = $app->make(StorageLocationFactory::class)->fetchByID((int) $fslID);
        if (!is_object($location)) {
            throw new Exception(t('Invalid file storage location.'));
        }

        $db = $app->make(Connection::class);
        $db->replace('TreeFileFolderNodes', [
            'treeNodeID' => $this->getTreeNodeID(),
            'fslID' => (int) $fslID,
        ], ['treeNodeID'], true);
        $this->fslID = (int) $fslID;
    }

    public function loadDetails()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $row = $db->fetchAssoc('SELECT * FROM TreeFileFolderNodes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);
        if (!empty($row)) {
            $this->setPropertiesFromArray($row);
        }
    }

    public function deleteDetails()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('DELETE FROM TreeFileFolderNodes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);
    }

    /**
     * @param \Concrete\Core\Tree\Node\Node|bool $parent Node's parent folder
     *
     * @return \Concrete\Core\Tree\Node\Node
     */
    public function duplicate($parent = false)
    {
        $node = static::add($this->getTreeNodeName(), $parent, $this->getTreeNodeStorageLocationID());
        $this->duplicateChildren($node);

        return $node;
    }

    public function getTreeNodeMenu()
    {
        return new FileFolderMenu($this);
    }

    public function getTreeNodeJSON()
    {
        $node = TreeNode::getTreeNodeJSON();
        if ($node) {
            $node->isFolder = true;
            $node->resultsThumbnailImg = $this->getListFormatter()->getIconElement();
        }

        return $node;
    }

    public function getListFormatter()
    {
        return new CategoryListFormatter();
    }

    /**
     * @param string $treeNodeName Node name
     * @param \Concrete\Core\Tree\Node\Node|bool $parent Node's parent folder
     * @param int|\Concrete\Core\Entity\File\StorageLocation\StorageLocation|null $storageLocationID Id or object of the storage location, if null the default one will be used
     *
     * @return \Concrete\Core\Tree\Node\Node
     */
    public static function add($treeNodeName = '', $parent = false, $storageLocationID = null)
    {
        // Get the storage location id if we have an object
        if (is_object($storageLocationID) && $storageLocationID instanceof \Concrete\Core\Entity\File\StorageLocation\StorageLocation) {
            $storageLocationID = $storageLocationID->getID();
        }

        // If its not empty verify its a real location
        elseif (!empty($storageLocationID)) {
            $app = Application::getFacadeApplication();
            $storageLocation = $app->make(StorageLocationFactory::class)->fetchByID((int) $storageLocationID);
            if (is_object($storageLocation)) {
                $storageLocationID = $storageLocation->getID();
            } else {
                $storageLocationID = null;
            }
        } else {
            $storageLocationID = null;
        }
        $node = parent::add($parent);
        $node->setTreeNodeName($treeNodeName);

        // Only set storage location if we have one
        if (!empty($storageLocationID)) {
            $node->setTreeNodeStorageLocationID($storageLocationID);
        }

        return $node;
    }

    public function getTreeNodeName()
    {
        if ($this->getTreeNodeParentID() == 0) {
            return t('File Manager');
        }

        return parent::getTreeNodeName();
    }

    public function getFolderItemList(User $u, Request $request)
    {
        $available = new Available();
        $sort = false;
        $list = new FolderItemList();
        $list->filterByParentFolder($this);
        if ($u !== null) {
            if (($column = $request->get($list->getQuerySortColumnParameter())) && ($direction = $request->get($list->getQuerySortDirectionParameter()))) {
                if (is_object($available->getColumnByKey($column)) && ($direction == 'asc' || $direction == 'desc')) {
                    $sort = [$column, $direction];
                    $u->saveConfig(sprintf('file_manager.sort.%s', $this->getTreeNodeID()), json_encode($sort));
                }
            } else {
                $sort = $u->config(sprintf('file_manager.sort.%s', $this->getTreeNodeID()));
                if ($sort) {
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    $sort = json_decode($sort);
                }
            }
            if (is_array($sort)) {
                if ($sortColumn = $available->getColumnByKey($sort[0])) {
                    $sortColumn->setColumnSortDirection($sort[1]);
                    $list->sortBySearchColumn($sortColumn);
                }
            }
        }

        return $list;
    }

    /**
     * Get the first child folder this folder that has a specific name.
     *
     * @param string $name The name of the child folder
     * @param bool $create Should the child folder be created if it does not exist?
     *
     * @return static|null return NULL if no child folder has the specified name and $create is false
     */
    public function getChildFolderByName($name, $create = false)
    {
        $typeHandle = $this->getTreeNodeTypeHandle();
        if ($this->childNodesLoaded) {
            $childNodes = $this->childNodes;
        } else {
            $childNodesData = $this->getHierarchicalNodesOfType($typeHandle, 1, true, false, 1);
            $childNodes = array_map(function ($item) {
                return $item['treeNodeObject'];
            }, $childNodesData);
        }
        $result = null;
        foreach ($childNodes as $childNode) {
            if ($childNode->getTreeNodeTypeHandle() === $typeHandle && $childNode->getTreeNodeName() === $name) {
                $result = $childNode;
                break;
            }
        }
        if ($result === null && $create) {
            $result = static::add($name, $this);
        }

        return $result;
    }

    /**
     * Get a descendent folder of this folder given its path.
     *
     * @param array $names The names of the child folders (1st item: child folder, 2nd item: grand-child folder, ...)
     * @param bool $create Should the descendent folders be created if they don't exist?
     *
     * @return static|null return NULL if the descendent folder has not been found and $create is false
     */
    public function getChildFolderByPath(array $names, $create = false)
    {
        if (count($names) === 0) {
            $result = $this;
        } else {
            $childName = array_shift($names);
            $result = $this->getChildFolderByName($childName, $create);
            if ($result !== null) {
                $result = $result->getChildFolderByPath($names, $create);
            }
        }

        return $result;
    }

    public function exportTranslations(Translations $translations)
    {
        return false;
    }
}
