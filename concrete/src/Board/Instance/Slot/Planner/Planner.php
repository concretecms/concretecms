<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Logger\LoggerFactory;
use Concrete\Core\Entity\Board\Instance;

class Planner
{

    const MAX_VERIFICATION_CHECKS = 15;
    const VERIFICATION_FAILURE_LOGGING_THRESHOLD = 5; // At what point do we start logging high numbers of failures.

    protected $verificationChecksRun = 0;

    /**
     * @var SlotFilterer
     */
    protected $slotFilterer;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(SlotFilterer $slotFilterer, LoggerFactory $loggerFactory)
    {
        $this->slotFilterer = $slotFilterer;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * @return int
     */
    public function getVerificationChecksRun(): int
    {
        return $this->verificationChecksRun;
    }

    protected function planSlot(
        PlannedInstance $plannedInstance,
        int $slot
    ): ?PlannedSlot {
        $logger = $this->loggerFactory->createFromInstance($plannedInstance->getInstance());
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
                $logger->write(
                    t(
                        'No template was able to be selected for slot %s on board instance %s. Total content objects remaining: %s',
                        $slot,
                        $plannedInstance->getInstance()->getBoardInstanceID(),
                        count($plannedInstance->getContentObjectGroups())
                    )
                );
            }
        } else {
            $logger->write(
                t('While planning slot %s on board instance %s no valid template choices were returned.',
                    $slot,
                    $plannedInstance->getInstance()->getBoardInstanceID()
                )
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
        $logger = $this->loggerFactory->createFromInstance($instance);
        $isValidInstance = null;
        $this->verificationChecksRun = 0;
        while ($isValidInstance !== true && $this->verificationChecksRun <= self::MAX_VERIFICATION_CHECKS) {
            if ($this->verificationChecksRun > self::VERIFICATION_FAILURE_LOGGING_THRESHOLD) {
                $logger->write(
                    t(
                        'High number of board planner verification checks on board instance generation for instance %s. Current check: %s',
                        $instance->getBoardInstanceID(),
                        $this->verificationChecksRun
                    )
                );
            }

            $this->verificationChecksRun++;
            $plannedInstance = $this->createPlannedInstance(
                $instance,
                $contentObjectGroups,
                $startingSlot,
                $totalSlots
            );
            $isValidInstance = $this->isValidInstance($plannedInstance);
        }

        if ($this->verificationChecksRun >= self::MAX_VERIFICATION_CHECKS) {
            $logger->write(
                t(
                    'Max verification checks limit of %s reached while generating board instance %s',
                    self::MAX_VERIFICATION_CHECKS,
                    $instance->getBoardInstanceID()
                )
            );
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

