<?php
namespace Concrete\Core\Board\Instance\Logger;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceLog;
use Doctrine\ORM\EntityManager;

class LoggerFactory implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, Repository $config)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function createNullLogger(): NullLogger
    {
        return new NullLogger();
    }

    public function createFromInstance(Instance $instance): LoggerInterface
    {
        if ($this->config->get('concrete.log.boards.instances')) {
            $log = $instance->getLog();
            if (!$log) {
                $log = new InstanceLog();
                $log->setInstance($instance);
                $this->entityManager->persist($log);
                $this->entityManager->flush();
                $instance->setLog($log);
                $this->entityManager->persist($instance);
                $this->entityManager->flush();
            }
            return $this->app->make(Logger::class, ['instanceLog' => $log]);
        } else {
            return $this->createNullLogger();
        }
    }
}
