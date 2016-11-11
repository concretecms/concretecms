<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\FileManager;

class FileFolder implements ObjectInterface
{
    protected $nodePath;

    public function __construct($nodePath)
    {
        $this->nodePath = $nodePath;
    }

    public function getPermissionObject()
    {
        $tree = FileManager::get();
        if ($this->nodePath) {
            $node = $tree->getNodeByPath($this->nodePath);
        } else {
            $node = $tree->getRootTreeNodeObject();
        }
        return $node;
    }


}
