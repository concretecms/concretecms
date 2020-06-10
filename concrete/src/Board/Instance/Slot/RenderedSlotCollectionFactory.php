<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Holds a collection of RenderedSlot objects, each bound to a particular slot for easy rendering.
 */
class RenderedSlotCollectionFactory
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function createCollection(Instance $instance)
    {
        $collectionArray = [];

        // First, let's fill the slots that are pinned.
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')->from(InstanceSlotRule::class, 'r')
            ->where('r.instance = :instance');
        // commenting this out because we want to grab pinned OR custom blocks. We may need more control
        //    ->andWhere('r.ruleType = :ruleType');
        // so I'm keeping this here.
        //$qb->setParameter('ruleType', InstanceSlotRule::RULE_TYPE_PINNED);
        $qb->setParameter('instance', $instance);
        $rules = $qb->getQuery()->execute();

        /**
         * @var $rule InstanceSlotRule
         */
        foreach($rules as $rule) {
            $slot = $rule->getSlot();
            $renderedSlot = new RenderedSlot($instance, $slot);
            $renderedSlot->setIsPinned(true);
            $renderedSlot->setBlockID($rule->getBlockID());
            $collectionArray[$slot] = $renderedSlot;
        }

        // Now let's automatically place everything else.
        $instanceSlots = $instance->getSlots()->toArray();
        // Before we get to that, let's remove anything from the instance slots array that has already been pinned
        // because we don't want to show it twice.
        $instanceSlots = array_filter($instanceSlots, function($instanceSlot) use ($collectionArray) {
            // this could probably be made more performant
            $keep = true;
            foreach($collectionArray as $collectionSlot) {
                if ($collectionSlot->getBlockID() == $instanceSlot->getBlockID()) {
                    // That means our pinned array already has this slot item in it. So let's not keep it.
                    $keep = false;
                }
            }
            return $keep;
        });

        // Now let's fill in everything else. If something attempts to go in one of the slots that is already full
        // we shift down until we can find an empty slot.
        $currentSlot = 1; // We start at slot one.
        foreach($instanceSlots as $instanceSlot) {
            if (isset($collectionArray[$currentSlot])) {
                // Is something pinned in this spot already? Let's increment spots until we get to an open slot.
                while(isset($collectionArray[$currentSlot])) {
                    $currentSlot++;
                }
            }
            $renderedSlot = new RenderedSlot($instance, $currentSlot);
            $renderedSlot->setBlockID($instanceSlot->getBlockID());
            $collectionArray[$currentSlot] = $renderedSlot;
            $currentSlot++;
        }

        $collection = new RenderedSlotCollection($instance, $collectionArray);
        return $collection;

    }

}

