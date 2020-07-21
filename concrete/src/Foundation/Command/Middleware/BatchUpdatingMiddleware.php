<?php

namespace Concrete\Core\Foundation\Command\Middleware;

use Concrete\Core\Foundation\Queue\Batch\BatchProgressUpdater;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Middleware;

class BatchUpdatingMiddleware implements Middleware
{
    /**
     * @var \Concrete\Core\Foundation\Queue\Batch\BatchProgressUpdater
     */
    protected $updater;

    public function __construct(BatchProgressUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\Tactician\Middleware::execute()
     */
    public function execute($command, callable $next)
    {
        $return = $next($command);

        if ($command instanceof BatchableCommandInterface) {
            $this->updater->incrementCommandProgress($command);
        }

        return $return;
    }
}
