<?php

namespace Concrete\Core\Messenger\Batch;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class BatchProcessUpdater
{

    const COLUMN_PENDING = 'pendingJobs';
    const COLUMN_TOTAL = 'totalJobs';
    const COLUMN_FAILED = 'failedJobs';

    /**
     * @var Application
     */
    protected $app;

    /**
     * BatchProgressUpdater constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        // FYI - I know that the actual dependency here is the entity manager, not the application object. But
        // unfortunately, we can't pass the entity manager in here because this dependency is created
        // too early in the booting process, and it screws up the tests.
        $this->app = $app;
    }

    public function updateJobs(string $batchProcessId, string $column, int $jobs)
    {
        if (!in_array($column, [self::COLUMN_TOTAL, self::COLUMN_FAILED, self::COLUMN_PENDING])) {
            throw new \Exception(t('Invalid column passed to BatchProcessUpdater::updateJobs: %s', $column));
        }

        $db = $this->app->make(Connection::class);
        if ($jobs < 0) {
            $jobs = abs($jobs);
            $db->executeUpdate(
                "update MessengerBatchProcesses set $column = $column - $jobs where id = ?",
                [
                    $batchProcessId
                ]
            );
        } else {
            $db->executeUpdate(
                "update MessengerBatchProcesses set $column = $column + $jobs where id = ?",
                [
                    $batchProcessId
                ]
            );
        }
    }

}
