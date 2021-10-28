<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
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

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(UuidGenerator $uuidGenerator, LoggerFactory $loggerFactory, User $user, Application $app, EntityManager $entityManager)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->app = $app;
        $this->user = $user;
        $this->entityManager = $entityManager;
        $this->loggerFactory = $loggerFactory;
    }

    public function __invoke(ScheduleCustomElementCommand $command)
    {
        $element = $command->getElement();
        $batchIdentifier = $this->uuidGenerator->generate($this->entityManager, $element);
        foreach($command->getInstances() as $instance) {
            $startDate = new \DateTime($command->getStartDateTime(), new \DateTimeZone($command->getTimezone()));
            if ($command->getEndDateTime()) {
                $endDate = new \DateTime($command->getEndDateTime(), new \DateTimeZone($command->getTimezone()));
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

            $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_BOARD);
            $logger->info(t('Element {elementName} scheduled for {slot} in instance {instanceID} successfully with start date {startDate} and lock type {lockType}'), [
                'slot' => $command->getSlot(),
                'instanceID' => $instance->getBoardInstanceID(),
                'startDate' => $command->getStartDateTime(),
                'elementName' => $element->getElementName(),
                'lockType' => $command->getLockType()
            ]);
        }
        $this->entityManager->flush();

    }

    
}
