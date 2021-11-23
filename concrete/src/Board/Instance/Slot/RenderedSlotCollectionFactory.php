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

    public function getCurrentRules(Instance $instance, array $ruleTypes = null)
    {
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone($instance->getSite()->getTimezone()));

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')->from(InstanceSlotRule::class, 'r')
            ->where('r.instance = :instance')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->lt('r.startDate', $now->getTimestamp()),
                $qb->expr()->eq('r.startDate', 0)
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->gt('r.endDate', $now->getTimestamp()),
                $qb->expr()->eq('r.endDate', 0)
            ));
        if (!is_null($ruleTypes)) {
            $qb->andWhere($qb->expr()->in('r.ruleType', $ruleTypes));
        }
        $qb->andWhere($qb->expr()->gt('r.slot', 0)); // Don't include draft rules
        $qb->setParameter('instance', $instance);
        $rules = $qb->getQuery()->execute();
        return $rules;
    }

    /**
     * @param array $ruleTypes All the InstanceSlotRule::CONSTANTS
     * @return RenderedSlot[]
     */
    protected function getRenderedSlotsFromRules(Instance $instance, array $ruleTypes)
    {
        $rules = $this->getCurrentRules($instance, $ruleTypes);
        $renderedSlots = [];
        /**
         * @var $rule InstanceSlotRule
         */
        foreach($rules as $rule) {
            $slot = $rule->getSlot();
            $renderedSlot = new RenderedSlot($instance, $slot);
            if ($rule->getRuleType() == $rule::RULE_TYPE_AUTOMATIC_SLOT_PINNED) {
                $renderedSlot->setSlotType($renderedSlot::SLOT_TYPE_PINNED);
            }
            if ($rule->getRuleType() == $rule::RULE_TYPE_DESIGNER_CUSTOM_CONTENT ||
                $rule->getRuleType() == $rule::RULE_TYPE_CUSTOM_CONTENT) {
                $renderedSlot->setSlotType($renderedSlot::SLOT_TYPE_CUSTOM);
            }
            $renderedSlot->setIsLocked($rule->isLocked());
            $renderedSlot->setBlockID($rule->getBlockID());
            $renderedSlot->setBoardInstanceSlotRuleID($rule->getBoardInstanceSlotRuleID());
            $renderedSlots[] = $renderedSlot;
        }

        return $renderedSlots;
    }


    public function createCollection(Instance $instance)
    {
        $collectionArray = [];

        $renderedSlotsEditors = $this->getRenderedSlotsFromRules($instance, [
            InstanceSlotRule::RULE_TYPE_AUTOMATIC_SLOT_PINNED, InstanceSlotRule::RULE_TYPE_CUSTOM_CONTENT
        ]);
        $renderedSlotsAdmins = $this->getRenderedSlotsFromRules($instance, [
            InstanceSlotRule::RULE_TYPE_DESIGNER_CUSTOM_CONTENT
        ]);

        // Now, let's merge these two arrays together, with the admin slots taking precedence.
        foreach($renderedSlotsEditors as $renderedSlot) {
            $collectionArray[$renderedSlot->getSlot()] = $renderedSlot;
        }
        foreach($renderedSlotsAdmins as $renderedSlot) {
            $collectionArray[$renderedSlot->getSlot()] = $renderedSlot;
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

        // Finally, let's add empty objects to slots if the board has slots that
        // are empty. This will allow us to use curation tools to work with those slots
        $availableSlots = $instance->getBoard()->getTemplate()->getDriver()->getTotalSlots();
        for ($i = 1; $i <= $availableSlots; $i++) {
            if (!isset($collectionArray[$i])) {
                $emptySlot = new RenderedSlot($instance, $i);
                $collectionArray[$i] = $emptySlot;
            }
        }

        $collection = new RenderedSlotCollection($instance, $collectionArray);
        return $collection;

    }

}

