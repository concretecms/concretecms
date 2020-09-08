<?php

namespace Concrete\Core\Summary;

use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Doctrine\ORM\EntityManager;

/**
 * Responsible for getting the category member from a summary object.
 */
class SummaryObjectInspector
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * A lightweight map of category handle/ids to inflated objects so we don't
     * have to have these thing be a complete performance nightmare
     *
     * @var array
     */
    protected $memberMap = [];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCategoryMember(SummaryObjectInterface $summaryObject): ?CategoryMemberInterface
    {
        if (!empty($this->memberMap[$summaryObject->getDataSourceCategoryHandle()][$summaryObject->getIdentifier()])) {
            return $this->memberMap[$summaryObject->getDataSourceCategoryHandle()][$summaryObject->getIdentifier()];
        }

        $r = $this->entityManager->getRepository(Category::class);
        $category = $r->findOneByHandle($summaryObject->getDataSourceCategoryHandle());
        if ($category) {
            $object = $category->getDriver()->getCategoryMemberFromIdentifier($summaryObject->getIdentifier());
            if ($object) {
                $this->memberMap[$summaryObject->getDataSourceCategoryHandle()][$summaryObject->getIdentifier(
                )] = $object;
                return $object;
            }
        }
        return null;
    }

}
