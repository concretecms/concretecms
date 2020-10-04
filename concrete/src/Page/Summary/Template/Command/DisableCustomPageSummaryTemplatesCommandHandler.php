<?php

namespace Concrete\Core\Page\Summary\Template\Command;

use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManager;

class DisableCustomPageSummaryTemplatesCommandHandler
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
        DisableCustomPageSummaryTemplatesCommand $command)
    {
        $this->clearCustomCollection($command->getPageID());
    }




}
