<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\File\Menu;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Menu\FileMenu;
use Concrete\Core\Tree\Node\Type\Menu\GroupMenu;
use Loader;

class File extends TreeNode
{
    protected $fID = null;


    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\FileTreeNodeResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\TreeNodeAssignment';
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'file_tree_node';
    }

    public function getTreeNodeTypeName()
    {
        return 'File';
    }

    public function getTreeNodeFileID()
    {
        return $this->fID;
    }

    public function getTreeNodeMenu()
    {
        $file = $this->getTreeNodeFileObject();
        if (is_object($file)) {
            return new Menu($file);
        }
    }

    public function getTreeNodeFileObject()
    {
        return \Concrete\Core\File\File::getByID($this->fID);
    }
    public function getTreeNodeName()
    {
        $f = \Concrete\Core\File\File::getByID($this->fID);
        if (is_object($f)) {
            return $f->getTitle();
        }
    }
    public function getTreeNodeDisplayName($format = 'html')
    {
        return $this->getTreeNodeName();
    }

    public function loadDetails()
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from TreeFileNodes where treeNodeID = ?', array($this->treeNodeID));
        $this->setPropertiesFromArray($row);
    }

    public function deleteDetails()
    {
        $f = $this->getTreeNodeFileObject();
        if (is_object($f)) {
            $f->delete(false);
        }
        $db = Loader::db();
        $db->Execute('delete from TreeFileNodes where treeNodeID = ?', array($this->treeNodeID));
    }

    public function getDateLastModified()
    {
        $f = $this->getTreeNodeFileObject();
        if (is_object($f)) {
            $version = $f->getVersion();
            if (is_object($version)) {
                return $version->getDateAdded();
            }
        }
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $file = $this->getTreeNodeFileObject();
            if (is_object($file)) {
                $json = $file->getJSONObject();
                if (is_object($json)) {
                    foreach($json as $key => $value) {
                        $obj->{$key} = $value;
                    }
                }
            }

            return $obj;
        }
    }

    public function setTreeNodeFile(\Concrete\Core\Entity\File\File $file)
    {
        $db = Loader::db();
        $db->Replace('TreeFileNodes', array('treeNodeID' => $this->getTreeNodeID(), 'fID' => $file->getFileID()), array('treeNodeID'), true);
        $this->fID = $file->getFileID();
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->getTreeNodeFileObject(), $parent);
        $this->duplicateChildren($node);

        return $node;
    }

    public function move(Node $newParent)
    {
        parent::move($newParent);
        $f = $this->getTreeNodeFileObject();
        if (is_object($f)) {
            $f->setFileFolder($newParent);
        }
    }

    public static function add($file = false, $parent = false)
    {
        $node = parent::add($parent);
        if (is_object($file)) {
            $node->setTreeNodeFile($file);
        }

        return $node;
    }
}
