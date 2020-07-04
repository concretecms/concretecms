<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Doctrine\ORM\EntityManager;

class ScheduleCustomElementCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    public function handle(ScheduleCustomElementCommand $command)
    {
        foreach($command->getInstances() as $instance) {
            $startDate = new \DateTime($command->getStartDate(), new \DateTimeZone($command->getTimezone()));
            if ($command->getEndDate()) {
                $endDate = new \DateTime($command->getEndDate(), new \DateTimeZone($command->getTimezone()));
            }
            $element = $command->getElement();
            $block = $element->createBlock();
            $boardCommand = new AddDesignerSlotToBoardCommand();
            if ($command->getLockType() == 'L') {
                $boardCommand->setLockType(InstanceSlotRule::RULE_TYPE_ADMIN_PUBLISHED_SLOT_LOCKED);
            } else {
                $boardCommand->setLockType(InstanceSlotRule::RULE_TYPE_ADMIN_PUBLISHED_SLOT);
            }
            $boardCommand->setSlot($command->getSlot());
            $boardCommand->setInstance($instance);
            $boardCommand->setBlockID($block->getBlockID());
            $boardCommand->setStartDate($startDate->getTimestamp());
            if (isset($endDate)) {
                $boardCommand->setEndDate($endDate->getTimestamp());
            }
            $this->app->executeCommand($boardCommand);
        }
    }

    
}
