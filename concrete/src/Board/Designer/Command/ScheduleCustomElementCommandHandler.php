<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

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

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    public function __construct(UuidGenerator $uuidGenerator, User $user, Application $app, EntityManager $entityManager)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->app = $app;
        $this->user = $user;
        $this->entityManager = $entityManager;
    }

    public function handle(ScheduleCustomElementCommand $command)
    {
        $element = $command->getElement();
        $batchIdentifier = $this->uuidGenerator->generate($this->entityManager, $element);
        foreach($command->getInstances() as $instance) {
            $startDate = new \DateTime($command->getStartDate(), new \DateTimeZone($command->getTimezone()));
            if ($command->getEndDate()) {
                $endDate = new \DateTime($command->getEndDate(), new \DateTimeZone($command->getTimezone()));
            }
            $block = $element->createBlock();
            $boardCommand = new AddDesignerSlotToBoardCommand();
            $boardCommand->setTimezone($command->getTimezone());
            $boardCommand->setRuleType(InstanceSlotRule::RULE_TYPE_DESIGNER_CUSTOM_CONTENT);
            if ($command->getLockType() == 'L') {
                $boardCommand->setIsLocked(true);
            }
            if ($this->user->isRegistered()) {
                $boardCommand->setUser($this->user->getUserInfoObject()->getEntityObject());
            }
            $boardCommand->setBatchIdentifier($batchIdentifier);
            $boardCommand->setNotes($element->getElementName());
            $boardCommand->setSlot($command->getSlot());
            $boardCommand->setInstance($instance);
            $boardCommand->setBlockID($block->getBlockID());
            $boardCommand->setStartDate($startDate->getTimestamp());
            if (isset($endDate)) {
                $boardCommand->setEndDate($endDate->getTimestamp());
            }
            $this->app->executeCommand($boardCommand);
        }
        $this->entityManager->flush();
    }

    
}
