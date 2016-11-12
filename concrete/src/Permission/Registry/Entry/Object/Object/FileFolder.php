<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\FileManager;

class FileFolder implements ObjectInterface
{
    protected $folder;

    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    public function getPermissionObject()
    {
        if (is_object($this->folder)) {
            return $this->folder;
        }

        $tree = FileManager::get();
        if ($this->folder) {
            $node = $tree->getNodeByDisplayPath($this->folder);
        } else {
            $node = $tree->getRootTreeNodeObject();
        }
        return $node;
    }


}
