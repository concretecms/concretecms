<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Doctrine\ORM\EntityManager;

class DeleteBoardInstanceSlotRuleCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory, EntityManager $entityManager)
    {
        $this->loggerFactory = $loggerFactory;
        $this->entityManager = $entityManager;
    }
    
    
    public function __invoke(DeleteBoardInstanceSlotRuleCommand $command)
    {
        $ruleID = $command->getRule()->getBoardInstanceSlotRuleID();
        $instance = $command->getRule()->getInstance();
        $this->entityManager->remove($command->getRule());
        $this->entityManager->flush();

        $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_BOARD);
        $logger->info(t('Instance Slot Rule {ruleID} for instance {instanceID} deleted from admin interface.'), [
            'ruleID' => $ruleID,
            'instanceID' => $instance->getBoardInstanceID(),
        ]);

    }

    
}
