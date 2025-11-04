<?php
namespace Concrete\Core\Express\Command;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Site\Service;
use Doctrine\ORM\EntityManager;

class PublishEntityCommandHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_EXPRESS;
    }

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(PublishEntityCommand $command)
    {
        $entity = $command->getEntity();
        $entity->setIsPublished(true);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->logger->info(t('Express entity %s (%s) published successfully.', $entity->getName(), $entity->getId()));
    }





}
