<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Doctrine\Common\Collections\ArrayCollection;

trait StandardCategoryTrait
{
    protected $categoryEntity;

    abstract public function getEntityManager();

    public function setCategoryEntity(Category $category)
    {
        $this->categoryEntity = $category;
    }

    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }

    public function getSetManager()
    {
        if (!isset($this->setManager)) {
            $this->setManager = new StandardSetManager($this->getCategoryEntity(), $this->getEntityManager());
        }

        return $this->setManager;
    }

    /**
     * @deprecated
     *
     * @param mixed $handle
     * @param mixed $name
     * @param mixed|null $pkg
     * @param mixed|null $locked
     */
    public function addSet($handle, $name, $pkg = null, $locked = null)
    {
        $manager = $this->getSetManager();

        return $manager->addSet($handle, $name, $pkg, $locked);
    }

    public function getAttributeTypes()
    {
        return $this->getCategoryEntity()->getAttributeTypes();
    }

    public function associateAttributeKeyType(AttributeType $type)
    {
        /**
         * @var ArrayCollection $types
         */
        $types = $this->getCategoryEntity()->getAttributeTypes();
        if (!$types->contains($type)) {
            $types->add($type);
        }
        $this->getEntityManager()->persist($this->getCategoryEntity());
        $this->getEntityManager()->flush();
    }

    public function delete()
    {
        $this->getEntityManager()->remove($this->getCategoryEntity());
        $this->getEntityManager()->flush();
    }
}
