<?php
namespace Concrete\Core\Page\Summary\Template;

use Concrete\Core\Entity\Summary\PageTemplate;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Template\Filterer;
use Doctrine\ORM\EntityManager;

/**
 * Responsible for taking an instance of a category's object (e.g. a Page object),
 * and saving all the available summary templates that can be used with that object.
 */
class PagePopulator 
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

    public function __construct(DriverManager $driverManager, EntityManager $entityManager, Filterer $filterer)
    {
        $this->driverManager = $driverManager;
        $this->entityManager = $entityManager;
        $this->filterer = $filterer;
    }

    public function updateAvailableSummaryTemplates(Page $page)
    {
        // First, delete any that currently exist
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(PageTemplate::class, 'pt')
            ->where('pt.cID = :cID');
        $queryBuilder->setParameter('cID', $page->getCollectionID());
        $queryBuilder->getQuery()->execute();
        
        /**
         * @var $driverCollection DriverCollection
         */
        $driverCollection = $this->driverManager->getDriverCollection($page);
        
        if ($driverCollection) {
            $data = $driverCollection->extractData($page);
            $templates = $this->filterer->getTemplates('page', $data);
            foreach($templates as $template) {
                $pageTemplate = new PageTemplate();
                $pageTemplate->setPageID($page->getCollectionID());
                $pageTemplate->setTemplate($template);
                $pageTemplate->setData($data);
                $this->entityManager->persist($pageTemplate);
            }
        }
        
        $this->entityManager->flush();
    }

}
