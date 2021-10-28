<?php

namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Tree\Node\Type\Menu\GroupFolderMenu;
use Concrete\Core\User\Group\FolderItemList;
use Concrete\Core\User\Group\GroupType;
use Concrete\Core\User\Group\Search\ColumnSet\Available;
use Concrete\Core\User\Group\Search\ColumnSet\FolderSet;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\User\User;
use Gettext\Translations;
use Symfony\Component\HttpFoundation\Request;

class GroupFolder extends TreeNode
{
    const CONTAINS_GROUP_FOLDERS = 0;
    const CONTAINS_GROUP_FOLDERS_AND_GROUPS = 1;
    const CONTAINS_SPECIFIC_GROUPS = 2;

    /**
     * @return int|null
     */
    protected $contains = self::CONTAINS_GROUP_FOLDERS_AND_GROUPS;

    /**
     * @return GroupType|null
     */
    protected $selectedGroupTypes = null;

    /**
     * @return int
     */
    public function getContains()
    {
        return $this->contains;
    }

    /**
     * @param int $contains
     */
    public function setContains($contains)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $db->replace('TreeGroupFolderNodes', [
            'treeNodeID' => $this->getTreeNodeID(),
            'contains' => (int)$contains,
        ], ['treeNodeID'], true);
        $this->contains = (int)$contains;
    }

    /**
     * @return GroupType[]
     */
    public function getSelectedGroupTypes()
    {
        return $this->selectedGroupTypes;
    }

    /**
     * @param GroupType[] $selectedGroupTypes
     */
    public function setSelectedGroupTypes($selectedGroupTypes)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $db->executeQuery('DELETE FROM TreeGroupFolderNodeSelectedGroupTypes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);

        foreach($selectedGroupTypes as $selectedGroupType) {
            $db->insert('TreeGroupFolderNodeSelectedGroupTypes', [
                'treeNodeID' => $this->getTreeNodeID(),
                'gtID' => $selectedGroupType->getId(),
            ]);
        }

        $this->selectedGroupTypes = $selectedGroupTypes;
    }

    public function getTreeNodeTranslationContext()
    {
        return 'TreeNodeGroupFolderName';
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\GroupFolderResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\GroupFolderAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'group_folder';
    }

    public function getTreeNodeTypeName()
    {
        return t('Folder');
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($this->getTreeNodeName()) {
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

    public function loadDetails()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $row = $db->fetchAssoc('SELECT * FROM TreeGroupFolderNodes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);
        if (!empty($row)) {
            $this->setPropertiesFromArray($row);
        }

        $this->selectedGroupTypes = [];

        foreach($db->fetchAll('SELECT gtID FROM TreeGroupFolderNodeSelectedGroupTypes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]) as $row) {
            $this->selectedGroupTypes[] = GroupType::getByID($row["gtID"]);
        }
    }

    public function deleteDetails()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('DELETE FROM TreeGroupFolderNodes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);
        $db->executeQuery('DELETE FROM TreeGroupFolderNodeSelectedGroupTypes WHERE treeNodeID = ?', [
            $this->treeNodeID,
        ]);
    }

    /**
     * @param TreeNode|bool $parent Node's parent folder
     *
     * @return TreeNode
     */
    public function duplicate($parent = false)
    {
        $node = static::add($this->getTreeNodeName(), $parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public function getTreeNodeMenu()
    {
        return new GroupFolderMenu($this);
    }

    public function getTreeNodeJSON()
    {
        $node = TreeNode::getTreeNodeJSON();
        if ($node) {
            $node->isFolder = true;
            $node->icon = 'fas fa-folder';
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
     * @param TreeNode|bool $parent Node's parent folder
     *
     * @param int $contains
     * @param GroupType[] $selectedGroupTypes
     * @return TreeNode|GroupFolder
     */
    public static function add($treeNodeName = '', $parent = false, $contains = self::CONTAINS_GROUP_FOLDERS, $selectedGroupTypes = [])
    {
        $node = parent::add($parent);
        $node->setTreeNodeName($treeNodeName);
        $node->setContains($contains);
        $node->setSelectedGroupTypes($selectedGroupTypes);

        return $node;
    }

    public function getTreeNodeName()
    {
        if ($this->getTreeNodeParentID() == 0) {
            return t('All Groups');
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
                    $u->saveConfig(sprintf('folder_manager.sort.%s', $this->getTreeNodeID()), json_encode($sort));
                }
            } else {
                $sort = $u->config(sprintf('folder_manager.sort.%s', $this->getTreeNodeID()));
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
