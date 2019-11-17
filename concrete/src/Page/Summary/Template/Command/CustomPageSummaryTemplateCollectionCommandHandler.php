<?php

namespace Concrete\Core\Page\Summary\Template\Command;

use Concrete\Core\Entity\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Summary\Template\PagePopulator;
use Doctrine\ORM\EntityManager;

class CustomPageSummaryTemplateCollectionCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var PagePopulator 
     */
    protected $pagePopulator;

    public function __construct(EntityManager $entityManager, PagePopulator $pagePopulator)
    {
        $this->entityManager = $entityManager;
        $this->pagePopulator = $pagePopulator;
    }
    
    protected function clearCustomCollection($pageID)
    {
        $collection = $this->entityManager->getRepository(CustomPageTemplateCollection::class)
            ->findOneByCID($pageID);
        if ($collection) {
            $collection->setTemplates(null);
            $this->entityManager->remove($collection);
            $this->entityManager->flush();
        }
    }

    public function handleEnableCustomPageSummaryTemplateCollectionCommand(
        EnableCustomPageSummaryTemplateCollectionCommand $command)
    {
        $this->clearCustomCollection($command->getPageID());
        $collection = new CustomPageTemplateCollection();
        $collection->setPageID($command->getPageID());
        
        $templateIDs = $command->getTemplateIDs();
        if (!empty($templateIDs)) {
            foreach($templateIDs as $templateID) {
                $template = $this->entityManager->find(Template::class,
                    $templateID
                );
                if ($template) {
                    $collection->getTemplates()->add($template);
                }
            }
        }
        
        $this->entityManager->persist($collection);
        $this->entityManager->flush();
    }

    public function handleDisableCustomPageSummaryTemplateCollectionCommand(
        DisableCustomPageSummaryTemplateCollectionCommand $command)
    {
        $this->clearCustomCollection($command->getPageID());
    }




}
