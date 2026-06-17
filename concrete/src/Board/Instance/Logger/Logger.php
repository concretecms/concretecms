<?php
namespace Concrete\Core\Board\Instance\Logger;

use Concrete\Core\Entity\Board\InstanceLog;
use Concrete\Core\Entity\Board\InstanceLogEntry;
use Doctrine\ORM\EntityManager;

class Logger implements LoggerInterface
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var InstanceLog
     */
    protected $instanceLog;

    public function __construct(EntityManager $entityManager, InstanceLog $instanceLog)
    {
        $this->entityManager = $entityManager;
        $this->instanceLog = $instanceLog;
    }

    public function write($message, ?object $data = null): void
    {
        $entry = new InstanceLogEntry();
        $entry->setLog($this->instanceLog);
        $entry->setMessage($message);
        if ($data) {
            $entry->setData($data);
        }
        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }
}
