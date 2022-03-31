<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Instance\ItemSegmenter;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\Slot\Planner\Planner;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\ORM\EntityManager;

class GenerateBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ContentPopulator
     */
    protected $contentPopulator;

    /**
     * @var ItemSegmenter
     */
    protected $itemSegmenter;

    /**
     * @var Planner
     */
    protected $planner;

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    public function __construct(
        EntityManager $entityManager,
        ItemSegmenter $itemSegmenter,
        ContentPopulator $contentPopulator,
        Planner $planner,
        JsonSerializer $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->itemSegmenter = $itemSegmenter;
        $this->contentPopulator = $contentPopulator;
        $this->planner = $planner;
        $this->serializer = $serializer;
    }

    public function __invoke(GenerateBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
        $items = $this->itemSegmenter->getBoardItemsForInstance($instance);
        $contentObjectGroups = $this->contentPopulator->createContentObjects($items);
        $boardTemplateDriver = $instance->getBoard()->getTemplate()->getDriver();
        $slotsToPlan = (int) round($boardTemplateDriver->getTotalSlots() * 1.5); // This will give us some wiggle room
        $plannedInstance = $this->planner->plan($instance, $contentObjectGroups, 1, $slotsToPlan);

        $blockType = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);
        foreach ($plannedInstance->getPlannedSlots() as $plannedSlot) {

            $plannedSlotTemplate = $plannedSlot->getTemplate();
            $slot = new InstanceSlot();
            $slot->setSlot($plannedSlot->getSlot());
            $slot->setInstance($instance);
            $slot->setTemplate($plannedSlotTemplate->getSlotTemplate());

            $json = $this->serializer->serialize($plannedSlotTemplate->getObjectCollection(), 'json');

            $data = [
                'contentObjectCollection' => $json,
                'slotTemplateID' => $plannedSlotTemplate->getSlotTemplate()->getId(),
            ];

            $block = $blockType->add($data);

            if ($block) {
                $slot->setBlockID($block->getBlockID());
            }

            $this->entityManager->persist($slot);
        }

        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }


}
