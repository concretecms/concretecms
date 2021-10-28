<?php
namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Doctrine\ORM\EntityManager;

/**
 * Responsible for taking a summary category handle, retrieving all the possible templates available for that handle
 * taking a collection of summary data fields, and returning only those templates that are available given their
 * summary field requirements
 */
class Filterer 
{
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $categoryHandle
     * @param Collection $collection
     * @return Template[]
     */
    public function getTemplates(string $categoryHandle, Collection $collection)
    {
        // First, retrieve the category entity
        $return = [];
        $category = $this->entityManager->getRepository(Category::class)
            ->findOneByHandle($categoryHandle);
        if ($category) {
            // Now, get templates assigned to this category
            $templates = $this->entityManager->getRepository(Template::class)
                ->findByCategory($category);
            if ($templates) {
                foreach($templates as $template) {
                    $include = true;
                    $fields = $template->getFields();
                    if ($fields) {
                        foreach ($fields as $field) {
                            $templateField = $field->getField();
                            if ($field->isRequired() && !$collection->containsField($templateField)) {
                                $include = false;
                            }
                        }
                    }
                    if ($include) {
                        $return[] = $template;
                    }
                }
            }
        }
        return $return;
    }

}
