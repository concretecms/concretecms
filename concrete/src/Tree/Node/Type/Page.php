<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Entity\Tree\Node\PageNode;
use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Assignment\TreeNodeAssignment;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Response\PageTreeNodeResponse;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Menu\MenuPageMenu;
use Doctrine\ORM\EntityManager;

class Page extends TreeNode implements NavigationMenuNodeInterface
{
    protected $cID = null;

    protected $includeSubpagesInMenu = false;

    public function getPermissionResponseClassName()
    {
        return PageTreeNodeResponse::class;
    }

    public function getPermissionAssignmentClassName()
    {
        return TreeNodeAssignment::class;
    }
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page_tree_node';
    }

    public function getTreeNodeTypeName()
    {
        return 'Page';
    }

    public function getTreeNodePageID()
    {
        return $this->cID;
    }

    public function getTreeNodeMenu()
    {
        return new MenuPageMenu($this);
    }

    public function getTreeNodePageObject()
    {
        return CorePage::getByID($this->cID);
    }
    public function getTreeNodeName()
    {
        $page = $this->getTreeNodePageObject();
        if ($page) {
            return $page->getCollectionName();
        }
    }
    public function getTreeNodeDisplayName($format = 'html')
    {
        if ($format === 'html') {
            return h($this->getTreeNodeDisplayName('text'));
        }

        return $this->getTreeNodeName();
    }

    public function loadDetails()
    {
        $em = app(EntityManager::class);
        $node = $em->find(PageNode::class, $this->getTreeNodeID());
        if ($node) {
            $this->cID = $node->getPageID();
            $this->includeSubpagesInMenu = $node->includeSubpagesInMenu();
        }
    }

    /**
     * @return mixed
     */
    public function includeSubpagesInMenu(): bool
    {
        return $this->includeSubpagesInMenu;
    }

    public function deleteDetails()
    {
        $em = app(EntityManager::class);
        $node = $em->find(PageNode::class, $this->getTreeNodeID());
        $em->remove($node);
        $em->flush();
    }

    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $page = $this->getTreeNodePageObject();
            if ($page) {
                $obj->cID = $page->getCollectionID();
                return $obj;
            }
        }
    }

    public function setDetails(CorePage $page, $includeSubpagesInMenu = false)
    {
        $em = app(EntityManager::class);
        $node = $em->find(PageNode::class, $this->getTreeNodeID());
        if (!$node) {
            $node = new PageNode();
            $node->setTreeNodeID($this->getTreeNodeID());
        }
        $node->setPageID($page->getCollectionID());
        $node->setIncludeSubpagesInMenu($includeSubpagesInMenu);
        $em->persist($node);
        $em->flush();
        $this->cID = $page->getCollectionID();
    }

    public function duplicate($parent = false)
    {
        $node = $this::add($this->getTreeNodePageObject(), $parent);
        $this->duplicateChildren($node);
        return $node;
    }

    public static function add($page = false, $includeSubpagesInMenu = false, $parent = false)
    {
        $node = parent::add($parent);
        if (is_object($page)) {
            $node->setDetails($page, $includeSubpagesInMenu);
        }
        return $node;
    }

    public function getNavigationItem(): ItemInterface
    {
        return new PageItem($this->getTreeNodePageObject());
    }

    public function canViewNavigationItem(): bool
    {
        $page = $this->getTreeNodePageObject();
        $permissions = new Checker($page);
        if ($permissions->canViewPage() && !$page->getAttribute('exclude_nav')) {
            return true;
        }
        return false;
    }
}
