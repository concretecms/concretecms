<?php
namespace Concrete\Core\Attribute\Category;

use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Category;

defined('C5_EXECUTE') or die("Access Denied.");

class CategoryFactory
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByHandle($akCategoryHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findOneBy(array('akCategoryHandle' => $akCategoryHandle));
    }

    public function getByID($akCategoryID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findOneBy(array('akCategoryID' => $akCategoryID));
    }

    public function getList()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Category');

        return $r->findAll();
    }

    public function add($akCategoryHandle, $allowSets, $pkg = null)
    {
        $category = new Category();
        $category->setAttributeKeyCategoryHandle($akCategoryHandle);
        $category->setAllowAttributeSets($allowSets);
        if ($pkg) {
            $category->setPackage($pkg);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $indexer = $category->getController()->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->createRepository($category->getController());
        }
    }
}
