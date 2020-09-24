<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Entity\Board\SlotTemplate;

/**
 * Responsible for filtering slot templates down to the appropriate ones given design criteria on the board,
 * total content slots in templates, etc...
 */
class SlotFilterer
{

    /**
     * @var AvailableTemplateCollectionFactory
     */
    protected $availableTemplateCollectionFactory;

    /**
     * SlotFilterer constructor.
     * @param AvailableTemplateCollectionFactory $availableTemplateCollectionFactory
     */
    public function __construct(
        AvailableTemplateCollectionFactory $availableTemplateCollectionFactory
    ) {
        $this->availableTemplateCollectionFactory = $availableTemplateCollectionFactory;
    }

    /**
     * Given an array of potential slot templates, let's reorder them so that we don't just have the same types of
     * templates showing up all the time.
     */
    protected function sortTemplatesForSlotNumber(array $validTemplatesForSlotNumber, int $slot): array
    {
        shuffle($validTemplatesForSlotNumber);
        return $validTemplatesForSlotNumber;
    }

    /**
     * Given a planned instance and a slot number, get all potential templates that could work. This filters out
     * templates not available for the current board instance, as well as templates that have more content slots
     * than what are remaining in the planned instance object.
     *
     * @param PlannedInstance $plannedInstance
     * @param int $slot
     * @return array
     */
    public function getPotentialSlotTemplates(PlannedInstance $plannedInstance, int $slot)
    {
        $planner = $plannedInstance->getInstance()->getBoard()->getTemplate()->getDriver()->getLayoutPlanner();
        $availableTemplates = $this->availableTemplateCollectionFactory->getAvailableTemplates(
            $plannedInstance->getInstance(),
            $slot
        );
        // Now that we have all available templates, let's create a subset that have fewer content slots
        // than the $contentObjectGroups. Obviously this isn't that important at the beginning of a board, but if we get
        // to the end of a board and we only have one item left we can't choose a template that has 2 slots in it.
        $validTemplatesForSlotNumber = [];
        foreach ($availableTemplates as $availableTemplate) {
            if ($availableTemplate->getDriver()->getTotalContentSlots() <= count(
                    $plannedInstance->getContentObjectGroups()
                )) {
                if (!$planner || ($planner->isValidTemplate($availableTemplate, $plannedInstance, $slot))) {
                    $validTemplatesForSlotNumber[] = $availableTemplate;
                }
            }
        }

        $validTemplatesForSlotNumber = $this->sortTemplatesForSlotNumber(
            $validTemplatesForSlotNumber,
            $slot
        );
        return $validTemplatesForSlotNumber;
    }

    protected function getPossibleContentObjectCollection(
        PlannedInstance $plannedInstance,
        SlotTemplate $potentialTemplate
    ) {
        $contentSlots = $potentialTemplate->getDriver()->getTotalContentSlots();
        // For the total number of slots on this template, grab a matching number of ItemObjectGroup
        // objects out of the $contentObjectGroups array.
        $objectGroups = $plannedInstance->sliceContentObjectGroups($contentSlots);
        // Do NOT shift these out of the $contentObjectGroups array yet because we don't know yet if they will work
        // for this template.

        $collection = new PossibleContentObjectCollection();
        for ($i = 0; $i < $contentSlots; $i++) {
            $availableContentObjectsForSlot = $objectGroups[$i];
            if ($availableContentObjectsForSlot) {
                $contentObjects = $availableContentObjectsForSlot->getContentObjects();
                $filterer = $potentialTemplate->getDriver()->getSlotFilterer();
                if ($filterer) {
                    $objects = $filterer->findContentObjectsForSlot($contentObjects, $i + 1);
                } else {
                    $objects = $contentObjects;
                }
                if (count($objects)) {
                    shuffle($objects);
                    $collection->addContentObjects($i + 1, $objects);
                }
            }
        }
        return $collection;
    }

    protected function createValidContentObjectCollection(
        PlannedInstance $plannedInstance,
        PossibleContentObjectCollection $possibleContentObjectCollection,
        int $slot
    ) {
        $planner = $plannedInstance->getInstance()->getBoard()->getTemplate()->getDriver()->getLayoutPlanner();
        $objectCollection = new ObjectCollection();
        foreach($possibleContentObjectCollection->getArray() as $slot => $contentObjects) {
            if (!empty($contentObjects[0])) {
                $objectCollection->addContentObject($slot, $contentObjects[0]);
            }
        }
        return $objectCollection;
    }

    public function findValidTemplateForSlot(
        PlannedInstance $plannedInstance,
        array $templateChoices,
        int $slot
    ): ?PlannedSlotTemplate {
        foreach ($templateChoices as $potentialTemplate) {
            $contentSlots = $potentialTemplate->getDriver()->getTotalContentSlots();
            $possibleContentObjectCollection = $this->getPossibleContentObjectCollection(
                $plannedInstance,
                $potentialTemplate
            );
            $contentObjectCollection = $this->createValidContentObjectCollection(
                $plannedInstance,
                $possibleContentObjectCollection,
                $slot
            );

            if (count($contentObjectCollection->getContentObjects()) == $contentSlots) {
                // We found a valid template. So let's remove the total number of content slots from our
                // $contentObjectGroups array so we don't just keep placing the same item over and over
                $plannedInstance->removeObjectGroups($contentSlots);
                $plannedSlotTemplate = new PlannedSlotTemplate();
                $plannedSlotTemplate->setSlotTemplate($potentialTemplate);
                $plannedSlotTemplate->setObjectCollection($contentObjectCollection);
                return $plannedSlotTemplate;
            }
        }
        // If we made it all the way down here, we must not be able to place any valid templates for the current
        // item. So let's remove an item and continue
        $plannedInstance->removeObjectGroups(1);
        if (count($plannedInstance->getContentObjectGroups()) > 0) {
            return $this->findValidTemplateForSlot($plannedInstance, $templateChoices, $slot);
        }
        return null;
    }
}

