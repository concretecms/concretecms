<?php

namespace Concrete\Core\Board\Instance\Notifier;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Site\Site;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractNotifier implements NotifierInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Concrete\Core\Entity\Board\Instance[]
     */
    protected function filterBySite(?Site $site): array
    {
        $qb = $this->entityManager->getRepository(Instance::class)->createQueryBuilder('i');
        if ($site) {
            $qb->where('i.site = :site')->setParameter('site', $site);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param \Concrete\Core\Entity\Board\Instance[] $instances
     *
     * @return \Concrete\Core\Entity\Board\Instance[]
     */
    protected function filterByHasConfiguration(array $instances, string $configurationClass): array
    {
        $return = [];
        foreach ($instances as $instance) {
            $board = $instance->getBoard();
            if ($board) {
                foreach ($board->getDataSources() as $configuredDataSource) {
                    $configuration = $configuredDataSource->getConfiguration();
                    if (is_a($configuration, $configurationClass)) {
                        $return[] = $instance;
                    }
                }
            }
        }

        return $return;
    }
}

