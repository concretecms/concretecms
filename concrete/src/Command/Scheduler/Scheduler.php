<?php

namespace Concrete\Core\Command\Scheduler;

use Concrete\Core\Config\Repository\Repository;

class Scheduler
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function isEnabled(): bool
    {
        return $this->config->get('concrete.processes.scheduler.enable');
    }


}
