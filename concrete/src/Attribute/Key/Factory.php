<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Doctrine\ORM\EntityManager;

class Factory
{

    protected $entityManager;
    protected $categoryService;

    public function __construct(CategoryService $categoryService, EntityManager $entityManager)
    {
        $this->categoryService = $categoryService;
        $this->entityManager = $entityManager;
    }


    public function getInstanceByID($akID)
    {
        return $this->getByID($akID);
    }

    public function getByID($akID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\Key')
            ->findOneBy(array('akID' => $akID));
    }

    public function getAttributeKeyList($category)
    {
        return $this->getList($category);
    }

    public function getList($category)
    {
        if (!is_object($category)) {
            $category = $this->categoryService->getByHandle($category);
            if (is_object($category)) {
                $category = $category->getController();
            }
        }

        if (is_object($category)) {
            /**
             * @var $category CategoryInterface
             */
            return $category->getList();
        }
    }

}
