<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Menu\FileFolderMenu;
use Concrete\Core\User\User;
use Gettext\Translations;
use Symfony\Component\HttpFoundation\Request;

class FileFolder extends Category
{
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

    public function getTreeNodeName()
    {
        if ($this->getTreeNodeParentID() == 0) {
            return t('File Manager');
        }

        return parent::getTreeNodeName();
    }

    public function getFolderItemList(User $u = null, Request $request)
    {
        $available = new FolderSet();
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
            $childNodes = array_map(function ($item) { return $item['treeNodeObject']; }, $childNodesData);
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
