<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;

class Planner implements LoggerAwareInterface
{

    const MAX_VERIFICATION_CHECKS = 15;
    const VERIFICATION_FAILURE_LOGGING_THRESHOLD = 5; // At what point do we start logging high numbers of failures.

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_CONTENT;
    }

    /**
     * @var SlotFilterer
     */
    protected $slotFilterer;

    public function __construct(SlotFilterer $slotFilterer)
    {
        $this->slotFilterer = $slotFilterer;
    }

    protected function planSlot(
        PlannedInstance $plannedInstance,
        int $slot
    ): ?PlannedSlot {
        $templateChoices = $this->slotFilterer->getPotentialSlotTemplates($plannedInstance, $slot);
        if (count($templateChoices)) {
            $selectedTemplate = $this->slotFilterer->findValidTemplateForSlot(
                $plannedInstance,
                $templateChoices,
                $slot
            );
            if ($selectedTemplate) {
                $plannedSlot = new PlannedSlot();
                $plannedSlot->setSlot($slot);
                $plannedSlot->setTemplate($selectedTemplate);
                return $plannedSlot;
            } else {
                $this->logger->notice(
                    t(
                        'No template was able to be selected for slot {slot} on board instance {instance}. Total content objects remaining: {remaining}'
                    ),
                    [
                        'slot' => $slot,
                        'instance' => $plannedInstance->getInstance()->getBoardInstanceID(),
                        'remaining' => count($plannedInstance->getContentObjectGroups())
                    ]
                );
            }
        } else {
            $this->logger->notice(
                t('While planning slot {slot} on board instance {instance}, no valid template choices were returned.'),
                ['slot' => $slot, 'instance' => $plannedInstance->getInstance()->getBoardInstanceID()]
            );
        }

        return null;
    }

    protected function createPlannedInstance(
        Instance $instance,
        array $contentObjectGroups,
        int $startingSlot,
        int $totalSlots
    ): PlannedInstance {
        $plannedInstance = new PlannedInstance($instance, $contentObjectGroups);
        for ($slot = $startingSlot; $slot <= $totalSlots; $slot++) {
            $plannedSlot = $this->planSlot($plannedInstance, $slot);
            if ($plannedSlot) {
                $plannedInstance->addPlannedSlot($plannedSlot);
            }
        }
        return $plannedInstance;
    }

    protected function isValidInstance(PlannedInstance $plannedInstance): bool
    {
        $planner = $plannedInstance->getInstance()->getBoard()->getTemplate()->getDriver()->getLayoutPlanner();
        if ($planner) {
            return $planner->isValidInstance($plannedInstance);
        }
        return true;
    }

    /**
     * @param Instance $instance
     * @param array $contentObjectGroups
     * @param int $startingSlot
     * @param int $totalSlots
     * @return PlannedInstance
     */
    public function plan(
        Instance $instance,
        array $contentObjectGroups,
        int $startingSlot,
        int $totalSlots
    ): PlannedInstance {
        $isValidInstance = null;
        $verificationChecks = 0;
        while ($isValidInstance !== true && $verificationChecks <= self::MAX_VERIFICATION_CHECKS) {
            if ($verificationChecks > self::VERIFICATION_FAILURE_LOGGING_THRESHOLD) {
                $this->logger->notice(
                    t(
                        'High number of board planner verification checks on board instance generation for instance {instance}. Current check: {checkNumber}'
                    ),
                    ['instance' => $instance->getBoardInstanceID(), 'checkNumber' => $verificationChecks]
                );
            }

            $verificationChecks++;
            $plannedInstance = $this->createPlannedInstance(
                $instance,
                $contentObjectGroups,
                $startingSlot,
                $totalSlots
            );
            $isValidInstance = $this->isValidInstance($plannedInstance);
        }

        if ($verificationChecks >= self::MAX_VERIFICATION_CHECKS) {
            throw new \Exception(
                t(
                    'Max verification checks limit of %s reached while generating board instance %s',
                    self::MAX_VERIFICATION_CHECKS,
                    $instance->getBoardInstanceID()
                )
            );
        }

        return $plannedInstance;
    }

}

