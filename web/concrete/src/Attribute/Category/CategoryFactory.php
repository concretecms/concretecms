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

    public function add($akCategoryHandle, $allowSets, $pkg = null)
    {
        $category = new Category();
        $category->setAttributeCategoryHandle($akCategoryHandle);
        $category->setAttributeCategoryAllowSets($allowSets);
        if ($pkg) {
            $category->setPackage($pkg);
        }
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}
