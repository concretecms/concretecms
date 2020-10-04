<?php

namespace Concrete\Core\Page\Summary\Template\Command;

use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManager;

class EnableCustomPageSummaryTemplatesCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

    public function __invoke(
        EnableCustomPageSummaryTemplatesCommand $command)
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


}
