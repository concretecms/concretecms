<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Category\CategoryFactory;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Doctrine\ORM\EntityManager;

class Factory
{

    protected $entityManager;
    protected $categoryFactory;

    public function __construct(CategoryFactory $categoryFactory, EntityManager $entityManager)
    {
        $this->categoryFactory = $categoryFactory;
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
            $category = $this->categoryFactory->getByHandle($category);
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
