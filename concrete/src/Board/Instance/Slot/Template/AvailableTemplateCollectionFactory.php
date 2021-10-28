<?php

namespace Concrete\Core\Board\Instance\Slot\Template;

use Concrete\Core\Entity\Board\Board;
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

    /**
     * @return SlotTemplate[]
     */
    public function getAllSlotTemplates()
    {
        return $this->entityManager->getRepository(SlotTemplate::class)->findAll();
    }

    public function getBoardSlotTemplates(Board $board)
    {
        if ($board->hasCustomSlotTemplates()) {
            $availableTemplates = $board->getCustomSlotTemplates();
        } else {
            $availableTemplates = $this->getAllSlotTemplates();
        }
        return $availableTemplates;
    }

    /**
     * @param Instance $instance
     * @param int $slot
     * @return SlotTemplate[]
     */
    public function getAvailableTemplates(Instance $instance, int $slot)
    {
        $templates = $this->getBoardSlotTemplates($instance->getBoard());
        $filteredTemplates = [];
        foreach ($templates as $template) {
            $filteredTemplates[] = $template;
        }
        return $filteredTemplates;
    }

}

