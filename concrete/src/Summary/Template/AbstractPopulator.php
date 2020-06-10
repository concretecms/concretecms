<?php
namespace Concrete\Core\Summary\Template;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Doctrine\ORM\EntityManager;

/**
 * Responsible for taking an instance of a category's object (e.g. a Page object),
 * and saving all the available summary templates that can be used with that object.
 */
abstract class AbstractPopulator
{
    /**
     * @var DriverManager
     */
    protected $driverManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Filterer
     */
    protected $filterer;
    
    abstract public function clearAvailableTemplates(CategoryMemberInterface $mixed);

    abstract public function createCategoryTemplate(CategoryMemberInterface $mixed);

    public function __construct(DriverManager $driverManager, EntityManager $entityManager, Filterer $filterer)
    {
        $this->driverManager = $driverManager;
        $this->entityManager = $entityManager;
        $this->filterer = $filterer;
    }

    public function updateAvailableSummaryTemplates(CategoryMemberInterface $mixed)
    {
        // First, delete any that currently exist
        $this->clearAvailableTemplates($mixed);
        
        /**
         * @var $driverCollection DriverCollection
         */
        $driverCollection = $this->driverManager->getDriverCollection($mixed);

        if ($driverCollection) {
            $data = $driverCollection->extractData($mixed);
            $templates = $this->filterer->getTemplates($mixed->getSummaryCategoryHandle(), $data);
            foreach($templates as $template) {
                $categoryTemplate = $this->createCategoryTemplate($mixed);
                $categoryTemplate->setTemplate($template);
                $categoryTemplate->setData($data);
                $this->entityManager->persist($categoryTemplate);
            }
        }
        $this->entityManager->flush();
    }

}
