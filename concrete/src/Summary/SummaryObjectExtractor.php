<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Summary\Data\Field\LazyDataFieldDataInterface;
use Doctrine\ORM\EntityManager;

/**
 * Responsible for extracting all data fields from a Collection object. Primarily exists in order to handle
 * lazy loading and more exotic data field use cases.
 */
class SummaryObjectExtractor
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function usesLazyLoading(array $fields)
    {
        return count(array_filter($fields, function($field) {
            return $field instanceof LazyDataFieldDataInterface;
        })) > 0;
    }

    public function getData(SummaryObject $summaryObject): array
    {
        $fields = $summaryObject->getData()->getFields();
        if (!$this->usesLazyLoading($fields)) {
            return $fields;
        }

        // If we're here, we have one or more fields that require lazy loading from the relevant source object.
        $r = $this->entityManager->getRepository(Category::class);
        $category = $r->findOneByHandle($summaryObject->getDataSourceCategoryHandle());
        if ($category) {
            $object = $category->getDriver()->getCategoryMemberFromIdentifier($summaryObject->getIdentifier());
            $resolvedFields = [];
            foreach ($fields as $identifier => $field) {
                $resolvedField = null;
                if ($field instanceof LazyDataFieldDataInterface) {
                    // Replace this proxy field with the real one
                    if ($object) {
                        $resolvedField = $field->loadDataFieldDataFromCategoryMember($object);
                    }
                } else {
                    $resolvedField = $field;
                }
                if ($resolvedField) {
                    $resolvedFields[$identifier] = $resolvedField;
                }
            }
        }
        return $resolvedFields;
    }

}
