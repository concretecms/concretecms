<?php

namespace Concrete\Core\Board\Instance\Slot\Template;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\ORM\EntityManager;

class AvailableTemplateCollectionFactory
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getAvailableTemplates(Instance $instance, int $slot)
    {
        $board = $instance->getBoard();
        if ($board->hasCustomSlotTemplates()) {
            $availableTemplates = $board->getCustomSlotTemplates();
        } else {
            $availableTemplates = $this->entityManager->getRepository(SlotTemplate::class)->findAll();
        }

        $availableTemplatesByFormFactor = [];
        foreach($availableTemplates as $availableTemplate) {
            $availableTemplatesByFormFactor[$availableTemplate->getFormFactor()][] = $availableTemplate;
        }

        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $formFactor = $driver->getFormFactor();
        if (is_array($formFactor)) {
            $formFactor = $formFactor[$slot];
        } else {
            $formFactor = $driver->getFormFactor();
        }
        $filteredTemplates = $availableTemplatesByFormFactor[$formFactor];
        return $filteredTemplates;
    }

}

