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

        $availableTemplates = $this->getBoardSlotTemplates($instance->getBoard());

        $availableTemplatesByFormFactor = [];
        foreach($availableTemplates as $availableTemplate) {
            $availableTemplatesByFormFactor[$availableTemplate->getFormFactor()][] = $availableTemplate;
        }

        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $formFactors = $driver->getFormFactor();
        if (is_array($formFactors)) {
            $formFactor = $formFactors[$slot];

            if (!$formFactor && $slot > $driver->getTotalSlots()) {
                // We're at the point of the loop where we're retrieving additional potential objects
                // for slots that we aren't specifically defining in our board. So let's do that by
                // re-looping through the slots we DO have as many times as we need to do.
                // $totalPagesOfSlots = if we're retrieving slot "22" of a board that only has three real
                // slots on it, that means we have to loop through our actual form factor loop 7 times.
                $totalPagesOfSlots = floor($slot / $driver->getTotalSlots());
                $totalToDiscard = $totalPagesOfSlots * $driver->getTotalSlots();
                $slotToCheck = $slot - $totalToDiscard;
                $formFactor = $formFactors[$slotToCheck];
            }
        } else {
            $formFactor = $formFactors;
        }
        $filteredTemplates = (array) $availableTemplatesByFormFactor[$formFactor];
        return $filteredTemplates;
    }

}

